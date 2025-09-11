<?php

namespace App\Http\Livewire\Admin\Oracle;

use Throwable;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LockMonitor extends Component
{

    public string $connection = 'oracle'; // sesuaikan nama koneksi di config/database.php


    // Filters
    public bool $onlyWaiting = true;
    public ?string $filterUser = null;
    public ?string $filterProgram = null;
    public ?int $minSecondsInWait = 5;


    public array $rows = [];
    public ?string $flash = null;
    public ?string $error = null;


    protected $listeners = [
        'confirmKill' => 'killSessionConfirmed',
    ];

    // Data untuk konfirmasi kill
    public ?int $killSid = null;
    public ?int $killSerial = null;


    public function mount()
    {
        $this->refreshData();
    }


    public function refreshData(): void
    {
        try {
            // Pakai ALL_OBJECTS jika tidak punya privilege ke DBA_OBJECTS
            $objectView = 'all_objects'; // ganti ke 'dba_objects' jika Anda punya aksesnya

            $sql = <<<'SQL'
    WITH locks AS (
        SELECT /* blockers */ l1.sid AS blocker_sid,
               l2.sid AS waiter_sid,
               l1.id1, l1.id2
        FROM v$lock l1
        JOIN v$lock l2
          ON l2.id1 = l1.id1 AND l2.id2 = l1.id2
        WHERE l1.block = 1 AND l2.block = 0
    )
    SELECT
        bs.sid              AS blocker_sid,
        bs.serial#          AS blocker_serial,
        bs.username         AS blocker_user,
        bs.program          AS blocker_program,
        bs.event            AS blocker_event,
        NVL(bs.seconds_in_wait,0) AS blocker_seconds_wait,

        ws.sid              AS waiter_sid,
        ws.serial#          AS waiter_serial,
        ws.username         AS waiter_user,
        ws.program          AS waiter_program,
        ws.event            AS waiter_event,
        NVL(ws.seconds_in_wait,0) AS waiter_seconds_wait,

        o.owner||'.'||o.object_name AS locked_object,
        o.object_type
    FROM locks k
    JOIN v$session bs ON bs.sid = k.blocker_sid
    JOIN v$session ws ON ws.sid = k.waiter_sid
    LEFT JOIN __OBJECT_VIEW__ o ON o.object_id = k.id1
    ORDER BY ws.seconds_in_wait DESC NULLS LAST
    SQL;

            // Sisipkan nama view objek sesuai privilege
            $sql = str_replace('__OBJECT_VIEW__', $objectView, $sql);

            $rows = DB::connection($this->connection)->select($sql);

            // Filter opsional di sisi PHP (cepat dan simpel)
            $filtered = collect($rows)
                ->map(function ($r) {
                    // Normalisasi key ke lowercase (OCI8 umumnya uppercase)
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
                        return $q === '' ? true : (str_contains($u, $q));
                    });
                })
                ->when($this->filterProgram, function ($c) {
                    $q = strtoupper(trim($this->filterProgram ?? ''));
                    return $c->filter(function ($r) use ($q) {
                        $p = strtoupper(($r['waiter_program'] ?? '') . ' ' . ($r['blocker_program'] ?? ''));
                        return $q === '' ? true : (str_contains($p, $q));
                    });
                })
                ->values()
                ->toArray();

            $this->rows = $filtered;
            $this->error = null;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            $this->rows = [];
        }
    }



    public function confirmKill(int $sid, int $serial): void
    {
        $this->killSid = $sid;
        $this->killSerial = $serial;
        $this->dispatchBrowserEvent('open-kill-confirm');
    }


    public function killSessionConfirmed(): void
    {
        if (!$this->killSid || !$this->killSerial) return;
        try {
            $sql = "ALTER SYSTEM KILL SESSION '" . $this->killSid . "," . $this->killSerial . "' IMMEDIATE";
            DB::connection($this->connection)->statement($sql);
            $this->flash = "Killed SID {$this->killSid}, SERIAL# {$this->killSerial}.";
            $this->error = null;
        } catch (Throwable $e) {
            $this->error = $e->getMessage();
            $this->flash = null;
        } finally {
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
