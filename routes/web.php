<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClienteController;

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

require __DIR__.'/auth.php';


//Controlador Cliente

Route::prefix('clientes')->name('clientes.')->group(function () {
    Route::get('/', [ClienteController::class, 'index'])->name('index');

    // Crear un cliente existente

    Route::get('create', [ClienteController::class, 'create'])->name('create');
    Route::post('store', [ClienteController::class, 'store'])->name('store');

    // Actualizar un cliente existente

    Route::get('{id}/edit', [ClienteController::class, 'edit'])->name('edit');
    Route::put('{id}', [ClienteController::class, 'update'])->name('update');

    // Eliminar un cliente

    Route::delete('{id}', [ClienteController::class, 'destroy'])->name('destroy');
});


