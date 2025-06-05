<?php

namespace App\Http\Livewire\MyAdmin\Permissions;

use Illuminate\Support\Facades\DB;

// use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use Spatie\Permission\PermissionRegistrar;
// use Illuminate\Support\Facades\Auth;
// use App\Models\User;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Livewire\WithPagination;

use Livewire\Component;


class Permissions extends Component
{
    use WithPagination;

    // primitive Variable
    public string $myTitle = 'Permission SIRus';
    public string $mySnipet = 'Data Permission ';

    public array $myData = ['name' => ''];

    // TopBar
    public array $myTopBar = [];

    public string $refSearch = '';
    // search logic -resetExcept////////////////
    protected $queryString = [
        'refSearch' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    // resetPage When refSearch is Typing
    public function updatedMytopbarRefsearch()
    {
        $this->resetPage();
    }
    public function updatedMytopbarRefDate()
    {
        $this->resetPage();
    }

    // open and close modal start////////////////
    //  modal status////////////////
    public bool $isOpen = false;
    public string $isOpenMode = 'insert';
    public bool $forceInsertRecord = false;
    //
    private function openModal(): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'insert';
    }
    private function openModalEdit($rjNo): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'update';
    }

    private function openModalTampil(): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'tampil';
    }

    public function closeModal(): void
    {
        $this->reset(['isOpen', 'isOpenMode']);
    }
    // open and close modal end////////////////


    public function createPermission(): void
    {
        $this->openModal();
        // $this->redirect('/register');
    }

    public function deletePermission($id): void
    {
        $deleted = Permission::Where('id', $id)->delete();
    }

    public function store()
    {
        $this->validate([
            'myData.name' => ['required', 'string', 'max:255'],
        ]);

        $Permission = Permission::create(['name' => $this->myData['name']]);
        $this->closeModal();
        $this->reset(['myData']);
    }

    public function mount()
    {
        $this->checkMounted();
    }

















    public $shareServer = '//172.8.8.12/rad_path/';
    public $mountPoint  = '/opt/lampp/htdocs/sirus-lite/storage/penunjang/rad';
    // Pesan status yang akan ditampilkan di view
    public $statusMessage = '';
    // Boolean: apakah folder sudah ter-mount?
    public $isMounted = 1;
    public function mountShare()
    {
        $cmd = [
            'sudo',
            '/usr/bin/mount',
            '-t',
            'cifs',
            $this->shareServer,
            $this->mountPoint,
        ];

        $process = new Process($cmd);

        try {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->statusMessage = "✓ Mount berhasil di: {$this->mountPoint}";
        } catch (ProcessFailedException $e) {
            $errorOutput = $process->getErrorOutput();
            $this->statusMessage = "✗ Mount gagal: " . trim($errorOutput);
        }

        // Setelah selesai, cek status mount kembali
        $this->checkMounted();
    }

    public function unmountShare()
    {
        $cmd = [
            'sudo',
            '/usr/bin/umount',
            $this->mountPoint,

        ];

        $process = new Process($cmd);

        try {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->statusMessage = "✓ Unmount berhasil dari: {$this->mountPoint}";
        } catch (ProcessFailedException $e) {
            $errorOutput = $process->getErrorOutput();
            $this->statusMessage = "✗ Unmount gagal: " . trim($errorOutput);
        }

        // Cek status lagi setelah unmount
        $this->checkMounted();
    }


    /**
     * Method untuk mengecek status mount.
     * Menggunakan perintah 'mountpoint -q'.
     * Jika exitCode = 0 → sudah ter-mount; jika != 0 → belum ter-mount.
     */
    public function checkMounted()
    {
        $cmd = [
            'sudo',
            '/usr/bin/mountpoint',
            '-q',
            $this->mountPoint
        ];

        $process = new Process($cmd);
        $process->run();

        if ($process->getExitCode() === 0) {
            $this->isMounted = 1;
        } else {
            $this->isMounted = 0;
        }
    }

    public function render()
    {
        // set mySearch
        $mySearch = $this->refSearch;

        // myQuery  /Collection
        $myQueryData = DB::table('permissions')
            ->select(
                'id',
                'name',
                'guard_name',
                'created_at',
                'updated_at',

            );

        $myQueryData->where(function ($q) use ($mySearch) {
            $q->orWhere(DB::raw('upper(name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(id)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(guard_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })

            ->orderBy('name', 'asc');
        // myQuery


        return view(
            'livewire.my-admin.permissions.permissions',
            ['myQueryData' => $myQueryData->paginate(10)]
        );
    }
}
