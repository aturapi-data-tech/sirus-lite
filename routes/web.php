<?php

use App\Http\Controllers\ProfileController;
use App\Http\Livewire\Post\PostTable;
use Illuminate\Support\Facades\Route;

use App\Http\Livewire\MasterLevelSatu\MasterLevelSatu;
use App\Http\Livewire\MasterLevelDua\MasterLevelDua;



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
Route::get('posts', PostTable::class)->middleware('auth')->name('posts');
Route::get('masterlevelsatu', MasterLevelSatu::class)->middleware('auth')->name('MasterLevelSatu');
Route::get('masterleveldua', MasterLevelDua::class)->middleware('auth')->name('MasterLevelDua');


// Life Cycle Hook
Route::get('/list', function () {
    return view('base', []);
});


require __DIR__ . '/auth.php';