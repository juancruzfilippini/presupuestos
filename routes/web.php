<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PresupuestoController;
use App\Http\Controllers\Admin\PrestacionController;
use App\Http\Controllers\Admin\ConvenioController;

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
    return view('auth.login');
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

Route::get('/presupuestos', [PresupuestoController::class, 'index'])->name('presupuestos.index');

Route::get('/presupuestos/create', [PresupuestoController::class, 'create'])->name('presupuestos.create');

Route::post('/presupuestos', [PresupuestoController::class, 'store'])->name('presupuestos.store');

Route::get('/presupuestos/edit/{id}', [PresupuestoController::class, 'edit'])->name('presupuestos.edit');

Route::post('/presupuestos/update/{id}', [PresupuestoController::class, 'update'])->name('presupuestos.update');

Route::delete('/presupuestos/{id}', [PresupuestoController::class, 'destroy'])->name('presupuestos.destroy');

Route::get('/search-patient', [PresupuestoController::class, 'searchPatient'])->name('presupuestos.searchPatient');

//Route::get('/searchPrestaciones', [PrestacionController::class, 'searchPrestaciones'])->name('searchPrestaciones');

Route::get('/getConvenios', [ConvenioController::class, 'getConvenios'])->name('getConvenios');

Route::get('/getPrestaciones/{convenioId}', [PrestacionController::class, 'getPrestaciones'])->name('getPrestaciones');

Route::get('/obtenerPrecio/{convenioId}/{codigoPrestacion}', [PrestacionController::class, 'obtenerPrecio'])->name('obtenerPrecio');

Route::get('/presupuestos/firmar/{id}', [PresupuestoController::class, 'firmar'])->name('presupuestos.firmar');

Route::get('estudios/exportar-datos/{id}', [ExportarController::class, 'exportarDatos'])->name('presupuesto.exportarDatos');

Route::get('/presupuestos/sign/{id}/{rol_id}', [PresupuestoController::class, 'sign'])->name('presupuestos.sign');



