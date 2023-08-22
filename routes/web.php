<?php

use App\Http\Controllers\ProfileController;
use App\Http\Livewire\Post\PostTable;
use Illuminate\Support\Facades\Route;

use App\Http\Livewire\MasterPasien\MasterPasien;
use App\Http\Livewire\PendaftaranMandiriPasienPoli\PendaftaranMandiriPasienPoli;
use App\Http\Livewire\DaftarRJ\DaftarRJ;
use App\Http\Livewire\RJskdp\RJskdp;

use App\Http\Livewire\MrRJ\Skdp\Skdp;
use App\Http\Livewire\MrRJ\Screening\Screening;



use App\Http\Livewire\PelayananRJ\PelayananRJ;
use App\Http\Livewire\EmrRJ\EmrRJ;

use App\Http\Livewire\SetupHfisBpjs\SetupHfisBpjs;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::resource('posts', Post::class)
//     ->only(['index', 'store'])
//     ->middleware(['auth', 'verified']);





Route::get('pendaftaranMandiriPasienPoli', PendaftaranMandiriPasienPoli::class)->middleware('auth')->name('pendaftaranMandiriPasienPoli');
Route::view('/cetak-tiket', 'livewire.pendaftaran-mandiri-pasien-poli.cetak-tiket');

Route::get('MasterPasien', MasterPasien::class)->middleware('auth')->name('MasterPasien');

Route::get('daftarRJ', DaftarRJ::class)->middleware('auth')->name('daftarRJ');
Route::view('/cetak-sep', 'livewire.daftar-r-j.cetak-sep');

Route::get('RJskdp', RJskdp::class)->middleware('auth')->name('RJskdp');

// MR
Route::get('MrRJ/Skdp', Skdp::class)->middleware('auth')->name('MRRJskdp');
Route::get('MrRJ/Screening', Screening::class)->middleware('auth')->name('MRRJScreening');


Route::get('pelayananRJ', PelayananRJ::class)->middleware('auth')->name('pelayananRJ');



Route::get('SetupHfisBpjs', SetupHfisBpjs::class)->middleware('auth')->name('SetupHfisBpjs');

Route::get('EmrRJ', EmrRJ::class)->middleware('auth')->name('EmrRJ');




// Life Cycle Hook
Route::get('/list', function () {
    return view('base', []);
});


require __DIR__ . '/auth.php';
