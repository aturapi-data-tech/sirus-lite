<?php

namespace App\Http\Livewire\MyAdmin\Users;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\Rules;

use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

use Livewire\Component;


class Users extends Component
{
    use WithPagination;

    // primitive Variable
    public string $myTitle = 'User SIRus';
    public string $mySnipet = 'Data User ';

    public array $myData = ['name' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''];

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


    public function createUser(): void
    {
        $this->openModal();
        // $this->redirect('/register');
    }

    public function deleteUser($id): void
    {
        // $deleted = User::Where('email', $id)->delete();
    }

    public function store()
    {
        $this->validate([
            'myData.name' => ['required', 'string', 'max:255'],
            'myData.email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class . ',email'],
            'myData.password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $this->myData['name'],
            'email' => $this->myData['email'],
            'password' => Hash::make($this->myData['password']),
        ]);
        $this->closeModal();
        $this->reset(['myData']);
    }
    public function assignRolePerawat($id)
    {
        $user = new User;
        $user = $user->where('id', $id)->first();
        $user->assignRole('Perawat');
    }
    public function assignRoleDokter($id)
    {
        $user = new User;
        $user = $user->where('id', $id)->first();
        $user->assignRole('Dokter');
    }
    public function removeRolePerawat($id)
    {
        $user = new User;
        $user = $user->where('id', $id)->first();
        $user->removeRole('Perawat');
    }
    public function removeRoleDokter($id)
    {
        $user = new User;
        $user = $user->where('id', $id)->first();
        $user->removeRole('Dokter');
    }

    public function mount()
    {
    }

    public function render()
    {
        // set mySearch
        $mySearch = $this->refSearch;

        // myQuery  /Collection
        $myQueryData = DB::table('users')
            ->select(
                'id',
                'name',
                'email',
                'email_verified_at',
                'password',
                'created_at',
                'updated_at',

            );

        $myQueryData->where(function ($q) use ($mySearch) {
            $q->orWhere(DB::raw('upper(name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(id)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(email)'), 'like', '%' . strtoupper($mySearch) . '%');
        })

            ->orderBy('name', 'asc');
        // myQuery


        return view(
            'livewire.my-admin.users.users',
            ['myQueryData' => $myQueryData->paginate(10)]
        );
    }
}
