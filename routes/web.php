<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PresupuestoController;
use App\Http\Controllers\Admin\ExportarController;
use App\Http\Controllers\Admin\PrestacionController;
use App\Http\Controllers\Admin\ConvenioController;
use App\Http\Controllers\MailController;

use App\Http\Controllers\Admin\AdministradorController;

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
    return view('auth/login');
})->name('login');

Route::get('/presupuestos', [PresupuestoController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('presupuestos');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/presupuestos', [PresupuestoController::class, 'index'])->name('presupuestos.index');

    Route::get('/presupuestos/create', [PresupuestoController::class, 'create'])->name('presupuestos.create');

    Route::post('/presupuestos', [PresupuestoController::class, 'store'])->name('presupuestos.store');

    Route::get('/presupuestos/edit/{id}', [PresupuestoController::class, 'edit'])->name('presupuestos.edit');

    Route::post('/presupuestos/update/{id}', [PresupuestoController::class, 'update'])->name('presupuestos.update');

    Route::get('/presupuestos/farmacia/{id}', [PresupuestoController::class, 'farmacia'])->name('presupuestos.farmacia');

    Route::post('/presupuestos/updateFarmacia/{id}', [PresupuestoController::class, 'updateFarmacia'])->name('presupuestos.updateFarmacia');

    Route::get('/presupuestos/anestesia/{id}', [PresupuestoController::class, 'anestesia'])->name('presupuestos.anestesia');

    Route::post('/presupuestos/updateAnestesia/{id}', [PresupuestoController::class, 'updateAnestesia'])->name('presupuestos.updateAnestesia');

    Route::delete('/presupuestos/{id}', [PresupuestoController::class, 'destroy'])->name('presupuestos.destroy');

    Route::get('/search-patient', [PresupuestoController::class, 'searchPatient'])->name('presupuestos.searchPatient');

    //Route::get('/searchPrestaciones', [PrestacionController::class, 'searchPrestaciones'])->name('searchPrestaciones');

    Route::get('/getConvenios', [ConvenioController::class, 'getConvenios'])->name('getConvenios');

    Route::get('/getPrestaciones/{convenioId}', [PrestacionController::class, 'getPrestaciones'])->name('getPrestaciones');

    Route::get('/getAnestesias/{convenioId}', [PrestacionController::class, 'getAnestesias'])->name('getAnestesias');

    Route::get('/obtenerPrecio/{convenioId}/{codigoPrestacion}', [PrestacionController::class, 'obtenerPrecio'])->name('obtenerPrecio');

    Route::get('/presupuestos/firmar/{id}', [PresupuestoController::class, 'firmar'])->name('presupuestos.firmar');

    Route::get('presupuestos/exportar-datos/{id}', [ExportarController::class, 'exportarDatos'])->name('presupuestos.exportarDatos');

    Route::get('/presupuestos/sign/{id}/{rol_id}', [PresupuestoController::class, 'sign'])->name('presupuestos.sign');

    Route::get('/presupuestos/admin', [AdministradorController::class, 'adminView'])->name('presupuestos.admin');

    Route::post('/guardarConvenio', [AdministradorController::class, 'updateConvenio'])->name('presupuestos.convenio');

    Route::post('/presupuestos/{id}/guardar-archivo', [PresupuestoController::class, 'guardarArchivo'])->name('presupuestos.guardarArchivo');

    Route::get('/presupuestos/enviar-datos/{id}', [ExportarController::class, 'enviarDatosPorCorreo'])->name('enviar.datos');

    Route::delete('/deletePrestacion/{id}', [PresupuestoController::class, 'deletePrestacion'])->name('deletePrestacion');

    Route::get('/presupuestos/profesionales', [PresupuestoController::class, 'gestionarProfesionales'])->name('presupuestos.profesionales');

    Route::post('/presupuestos/profesionales', [PresupuestoController::class, 'guardarProfesional'])->name('profesionales.guardar');

    Route::delete('/presupuestos/profesionales/{id}', [PresupuestoController::class, 'eliminarProfesional'])->name('profesionales.eliminar');
});
