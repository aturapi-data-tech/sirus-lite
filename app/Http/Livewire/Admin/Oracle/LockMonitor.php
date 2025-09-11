<?php

namespace App\Http\Livewire\Admin\Oracle;

use Throwable;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LockMonitor extends Component
{
    public string $connection = 'oracle';

    // Filters
    public bool $onlyWaiting = true;
    public ?string $filterUser = null;
    public ?string $filterProgram = null;
    public ?int $minSecondsInWait = 5;

    public array $rows = [];

    // Modal/konfirmasi kill (kalau dipakai)
    public bool $showConfirm = false;
    public ?int $killSid = null;
    public ?int $killSerial = null;

    protected $listeners = [
        'confirmKill' => 'killSessionConfirmed',
    ];

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        try {
            $objectView = 'all_objects'; // ganti ke 'dba_objects' jika punya privilege

            $sql = <<<'SQL'
            WITH locks AS (
                SELECT l1.sid AS blocker_sid,
                       l2.sid AS waiter_sid,
                       l1.id1, l1.id2
                FROM v$lock l1
                JOIN v$lock l2
                  ON l2.id1 = l1.id1 AND l2.id2 = l1.id2
                WHERE l1.block = 1 AND l2.block = 0
            )
            SELECT
                -- BLOCKER
                bs.sid                      AS blocker_sid,
                bs.serial#                  AS blocker_serial,
                bs.username                 AS blocker_user,
                bs.program                  AS blocker_program,
                bs.module                   AS blocker_module,
                bs.machine                  AS blocker_machine,
                bs.event                    AS blocker_event,
                NVL(bs.seconds_in_wait,0)   AS blocker_seconds_wait,
                NVL(bs.sql_id, bs.prev_sql_id) AS blocker_sql_id,
                SUBSTR(sb.sql_text,1,1000)  AS blocker_sql_text,

                -- WAITER
                ws.sid                      AS waiter_sid,
                ws.serial#                  AS waiter_serial,
                ws.username                 AS waiter_user,
                ws.program                  AS waiter_program,
                ws.module                   AS waiter_module,
                ws.machine                  AS waiter_machine,
                ws.event                    AS waiter_event,
                NVL(ws.seconds_in_wait,0)   AS waiter_seconds_wait,
                NVL(ws.sql_id, ws.prev_sql_id) AS waiter_sql_id,
                SUBSTR(sw.sql_text,1,1000)  AS waiter_sql_text,

                -- OBJECT
                o.owner||'.'||o.object_name AS locked_object,
                o.object_type
            FROM locks k
            JOIN v$session bs ON bs.sid = k.blocker_sid
            JOIN v$session ws ON ws.sid = k.waiter_sid
            LEFT JOIN __OBJECT_VIEW__ o ON o.object_id = k.id1
            LEFT JOIN v$sqlarea sb ON sb.sql_id = NVL(bs.sql_id, bs.prev_sql_id)
            LEFT JOIN v$sqlarea sw ON sw.sql_id = NVL(ws.sql_id, ws.prev_sql_id)
            ORDER BY ws.seconds_in_wait DESC NULLS LAST
            SQL;

            $sql = str_replace('__OBJECT_VIEW__', $objectView, $sql);

            $rows = DB::connection($this->connection)->select($sql);

            $this->rows = collect($rows)
                ->map(function ($r) {
                    $arr = (array) $r;
                    $norm = [];
                    foreach ($arr as $k => $v) {
                        $norm[strtolower($k)] = $v;
                    }
                    return $norm;
                })
                ->when($this->onlyWaiting, function ($c) {
                    $min = $this->minSecondsInWait ?? 0;
                    return $c->filter(fn($r) => (int)($r['waiter_seconds_wait'] ?? 0) >= $min);
                })
                ->when($this->filterUser, function ($c) {
                    $q = strtoupper(trim($this->filterUser ?? ''));
                    return $c->filter(function ($r) use ($q) {
                        $u = strtoupper(($r['waiter_user'] ?? '') . ' ' . ($r['blocker_user'] ?? ''));
                        return $q === '' ? true : str_contains($u, $q);
                    });
                })
                ->when($this->filterProgram, function ($c) {
                    $q = strtoupper(trim($this->filterProgram ?? ''));
                    return $c->filter(function ($r) use ($q) {
                        $p = strtoupper(($r['waiter_program'] ?? '') . ' ' . ($r['blocker_program'] ?? ''));
                        return $q === '' ? true : str_contains($p, $q);
                    });
                })
                ->values()
                ->toArray();

            // (Opsional) kasih info ringan
            // toastr()->closeOnHover(true)->closeDuration(3000)->positionClass('toast-top-left')->addInfo('Data locks diperbarui.');
        } catch (Throwable $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3000)
                ->positionClass('toast-top-left')
                ->addError([$e->getMessage()]);
            $this->rows = [];
        }
    }

    public function confirmKill(int $sid, int $serial): void
    {
        $this->killSid = $sid;
        $this->killSerial = $serial;
        $this->showConfirm = true; // kalau pakai modal sederhana Livewire v2
        // atau kalau tanpa modal: langsung panggil $this->killSessionConfirmed();
    }

    public function killSessionConfirmed(): void
    {
        if (!$this->killSid || !$this->killSerial) {
            toastr()->closeOnHover(true)
                ->closeDuration(3000)
                ->positionClass('toast-top-left')
                ->addError(['SID/Serial tidak valid.']);
            return;
        }

        try {
            $sql = "ALTER SYSTEM KILL SESSION '{$this->killSid},{$this->killSerial}' IMMEDIATE";
            DB::connection($this->connection)->statement($sql);

            toastr()->closeOnHover(true)
                ->closeDuration(3000)
                ->positionClass('toast-top-left')
                ->addSuccess("Killed SID {$this->killSid}, SERIAL# {$this->killSerial}.");
        } catch (Throwable $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3000)
                ->positionClass('toast-top-left')
                ->addError([$e->getMessage()]);
        } finally {
            $this->showConfirm = false;
            $this->killSid = null;
            $this->killSerial = null;
            $this->refreshData();
        }
    }

    public function render()
    {
        return view('livewire.admin.oracle.lock-monitor');
    }
}
