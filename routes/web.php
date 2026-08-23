<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AjusteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CarpetaController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();




Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);


Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    Route::get('/home', [Admin::class, 'index'])->name('home')->middleware('auth');
    Route::get('/admin', [Admin::class, 'index'])->name('admin.index')->middleware('auth');

    // Rutas para ajustes
    Route::get('/admin/ajustes', [App\Http\Controllers\AjusteController::class, 'index'])->name('admin.ajustes.index')->middleware( 'can:Ver formulario de ajustes');
    Route::post('/admin/ajustes', [App\Http\Controllers\AjusteController::class, 'store'])->name('admin.ajustes.store')->middleware( 'can:Editar ajustes');

    // Rutas para roles
    Route::get('/admin/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles.index')->middleware('can:Ver listado de roles');
    Route::post('/admin/roles/create', [App\Http\Controllers\RoleController::class, 'store'])->name('admin.roles.store')->middleware('can:Guardar rol');
    Route::get('/admin/roles/{id}/permisos', [App\Http\Controllers\RoleController::class, 'permisos'])->name('admin.roles.permisos')->middleware('can:Editar permisos de rol');
    Route::put('/admin/roles/{id}/update_permisos', [App\Http\Controllers\RoleController::class, 'updatePermisos'])->name('admin.roles.update_permisos')->middleware('can:Editar permisos de rol');
    Route::put('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('admin.roles.update')->middleware('can:Actualizar rol');
    Route::delete('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('admin.roles.destroy')->middleware('can:Eliminar rol');


    // Rutas para usuarios
    Route::get('/admin/users', [App\Http\Controllers\UserController::class, 'index'])->name('admin.users.index')->middleware( 'can:Ver listado de usuarios');
    Route::post('/admin/users/create', [App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store')->middleware( 'can:Guardar usuario');
    Route::put('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('admin.users.update')->middleware('can:Actualizar usuario');
    Route::delete('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('admin.users.destroy')->middleware('can:Eliminar usuario');


    // Rutas para Carpetas
    Route::get('/admin/carpetas/{id?}', [CarpetaController::class, 'index'])->name('admin.carpetas.index')->middleware( 'can:Ver listado de carpetas');
    Route::post('/admin/carpetas/create', [CarpetaController::class, 'store'])->name('admin.carpetas.store')->middleware( 'can:Crear carpeta');
    Route::post('/admin/carpetas/upload', [CarpetaController::class, 'uploadChunk'])->name('admin.carpetas.upload')->middleware( 'can:Subir archivos');
    Route::put('/admin/carpetas/{id}', [CarpetaController::class, 'update'])->name('admin.carpetas.update')->middleware( 'can:Editar elementos');
    Route::delete('/admin/carpetas/{id}', [CarpetaController::class, 'destroy'])->name('admin.carpetas.destroy')->middleware( 'can:Eliminar elementos');
    Route::post('/admin/carpetas/gestionar-accesos', [CarpetaController::class, 'gestionarAccesos'])->name('admin.carpetas.gestionarAccesos')->middleware( 'can:Compartir elementos');
    Route::post('/admin/carpetas/compartir', [CarpetaController::class, 'compartir'])->name('admin.carpetas.compartir')->middleware('can:Compartir elementos');

    // Rutas para Archivos
    Route::post('/admin/archivos/descargar-masivo', [CarpetaController::class, 'descargarMasivo'])->name('admin.archivos.descargarMasivo')->middleware( 'can:Descargar archivos');
    Route::get('/admin/archivos/descargar/{id}', [CarpetaController::class, 'descargarArchivo'])->name('archivo.download')->middleware( 'can:Descargar archivos');
    Route::post('/admin/archivos/renombrar', [CarpetaController::class, 'renombrar'])->name('admin.archivos.renombrar')->middleware( 'can:Editar elementos');
    Route::post('/admin/archivos/eliminar', [CarpetaController::class, 'eliminar'])->name('admin.archivos.eliminar')->middleware( 'can:Eliminar elementos');
    Route::post('/admin/archivos/mover', [CarpetaController::class, 'mover'])->name('admin.archivos.mover')->middleware( 'can:Editar elementos');
    Route::get('/admin/archivos/stream/{id}', [CarpetaController::class, 'streamVideo'])->name('admin.archivos.stream')->middleware( 'can:Descargar archivos');

});



