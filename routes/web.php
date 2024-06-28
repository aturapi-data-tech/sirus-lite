<?php

use App\Http\Controllers\ProfileController;
// use App\Http\Livewire\Post\PostTable;
use Illuminate\Support\Facades\Route;

use App\Http\Livewire\MasterPasien\MasterPasien;
use App\Http\Livewire\PendaftaranMandiriPasienPoli\PendaftaranMandiriPasienPoli;
use App\Http\Livewire\DaftarRJ\DaftarRJ;
use App\Http\Livewire\DaftarUGD\DaftarUGD;
// use App\Http\Livewire\DaftarUGD\DisplayPasien\DisplayPasien as DisplayPasienUGD;


use App\Http\Livewire\RJskdp\RJskdp;
use App\Http\Livewire\RIskdp\RIskdp;

use App\Http\Livewire\MrRJ\Skdp\Skdp;
use App\Http\Livewire\MrRJ\SkdpRI\SkdpRI;


// use App\Http\Livewire\MrRJ\Screening\Screening;
// use App\Http\Livewire\MrRJ\Anamnesia\Anamnesia;
// use App\Http\Livewire\MrRJ\Pemeriksaan\Pemeriksaan;
// use App\Http\Livewire\MrRJ\Penilaian\Penilaian;
// use App\Http\Livewire\MrRJ\Diagnosis\Diagnosis;
// use App\Http\Livewire\MrRJ\Perencanaan\Perencanaan;


// use App\Http\Livewire\EmrRJ\EresepRJ\EresepRJ;
// use App\Http\Livewire\EmrRJ\EresepRJ\EresepRJRacikan;



use App\Http\Livewire\PelayananRJ\PelayananRJ;
use App\Http\Livewire\DisplayPelayananRJ\DisplayPelayananRJ;
use App\Http\Livewire\EmrRJ\AdministrasiRJ\AdministrasiRJ;
use App\Http\Livewire\EmrRJ\EmrRJ;
use App\Http\Livewire\BookingRJ\BookingRJ;

use App\Http\Livewire\EmrRJ\TelaahResepRJ\TelaahResepRJ;


use App\Http\Livewire\EmrUGD\EmrUGD;
// use App\Http\Livewire\Emr\Laborat\Laborat;



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

use App\Http\Livewire\MyAdmin\Users\Users;
use App\Http\Livewire\MyAdmin\Roles\Roles;
use App\Http\Livewire\MyAdmin\Permissions\Permissions;

// Role Group
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/MyUsers', Users::class)->name('MyUsers');
    Route::get('/MyRoles', Roles::class)->name('MyRoles');
    Route::get('/MyPermissions', Permissions::class)->name('MyPermissions');
});

// Route::resource('posts', Post::class)
//     ->only(['index', 'store'])
//     ->middleware(['auth', 'verified']);





Route::get('pendaftaranMandiriPasienPoli', PendaftaranMandiriPasienPoli::class)->middleware('auth')->name('pendaftaranMandiriPasienPoli');
Route::view('/cetak-tiket', 'livewire.pendaftaran-mandiri-pasien-poli.cetak-tiket');

Route::get('MasterPasien', MasterPasien::class)->middleware('auth')->name('MasterPasien');

Route::get('daftarRJ', DaftarRJ::class)->middleware('auth')->name('daftarRJ');
Route::get('daftarUGD', DaftarUGD::class)->middleware('auth')->name('daftarUGD');

// Develop view
// Route::get('DisplayPasienUGD', DisplayPasienUGD::class)->middleware('auth')->name('DisplayPasienUGD');


Route::view('/cetak-sep', 'livewire.daftar-r-j.cetak-sep');
Route::view('/cetak-etiket', 'livewire.daftar-r-j.cetak-etiket');


Route::get('RJskdp', RJskdp::class)->middleware('auth')->name('RJskdp');
Route::get('RIskdp', RIskdp::class)->middleware('auth')->name('RIskdp');

// MR
// Route::get('MrRJ/Skdp', Skdp::class)->middleware('auth')->name('MRRJskdp');
// Route::get('MrRJ/SkdpRI', SkdpRI::class)->middleware('auth')->name('MRRJskdpRI');


// Route::get('MrRJ/Screening', Screening::class)->middleware('auth')->name('MRRJScreening');
// Route::get('MrRJ/Anamnesia', Anamnesia::class)->middleware('auth')->name('MRRJAnamnesia');
// Route::get('MrRJ/Pemeriksaan', Pemeriksaan::class)->middleware('auth')->name('MRRJPemeriksaan');
// Route::get('MrRJ/Penilaian', Penilaian::class)->middleware('auth')->name('MRRJPenilaian');
// Route::get('MrRJ/Diagnosis', Diagnosis::class)->middleware('auth')->name('MRRJDiagnosis');
// Route::get('MrRJ/Perencanaan', Perencanaan::class)->middleware('auth')->name('MRRJPerencanaan');

// Eresep
// Route::get('EmrRJ/Eresep', EresepRJ::class)->middleware('auth')->name('EmrRJEresepRJ');
// Route::get('EmrRJ/EresepRacikan', EresepRJRacikan::class)->middleware('auth')->name('EmrRJEresepRJ');
Route::get('EmrRJAdministrasi', AdministrasiRJ::class)->middleware('auth')->name('EmrRJAdministrasi');




Route::get('pelayananRJ', PelayananRJ::class)->middleware('auth')->name('pelayananRJ');
Route::get('displayPelayananRJ', displayPelayananRJ::class)->middleware('auth')->name('displayPelayananRJ');



Route::get('SetupHfisBpjs', SetupHfisBpjs::class)->middleware('auth')->name('SetupHfisBpjs');

Route::get('EmrRJ', EmrRJ::class)->middleware('auth')->name('EmrRJ');
Route::get('BookingRJ', BookingRJ::class)->middleware('auth')->name('BookingRJ');

Route::get('TelaahResepRJ', TelaahResepRJ::class)->middleware('auth')->name('TelaahResepRJ');

Route::get('EmrUGD', EmrUGD::class)->middleware('auth')->name('EmrUGD');
// Route::get('Laborat', Laborat::class)->middleware('auth')->name('Laborat');




// Life Cycle Hook
Route::get('/list', function () {
    return view('base', []);
});


require __DIR__ . '/auth.php';
