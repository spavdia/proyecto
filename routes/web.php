<?php

declare(strict_types=1);

use Sergio\Lib\Route;
use Sergio\App\controllers\HomeController;
use Sergio\App\Controllers\LeadController;
use Sergio\App\controllers\LoginController;
//rutas login y panel
Route::get('/', [HomeController::class, 'index']);
Route::get('/login', [LoginController::class, 'mostrarFormLogin']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);
Route::get('/panel', [HomeController::class, 'panel']);
//rutas formulario contacto
Route::get('/contacto', [LeadController::class, 'mostrarFormContacto']);
Route::post('/contacto', [LeadController::class, 'nuevoContacto']);
//rutas lead nuevo
Route::get('/leads/nuevo', [LeadController::class, 'mostrarFormLead']);
Route::post('/leads/guardar', [LeadController::class, 'nuevoLead']);
Route::post('/leads/cambiar-estado/{id}', [LeadController::class, 'cambiarEstado']);
//Rutas notas
Route::get('/leads/{id}', [LeadController::class, 'mostrarDetalle']);
Route::post('/leads/{id}/notas/guardar', [LeadController::class, 'nuevaNota']);
//editarLead o eliminar
Route::post("/leads/{id}/actualizar", [LeadController::class, 'actualizarLead']);
Route::post('/leads/{id}/eliminar', [LeadController::class, 'eliminarLead']);



Route::handleRoute();
