<?php

declare(strict_types=1);

use Sergio\Lib\Route;
use Sergio\App\Controllers\HomeController;
use Sergio\App\Controllers\LoginController;
use Sergio\App\Controllers\LeadController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/panel', [HomeController::class, 'panel']);
Route::get('/pipeline', [HomeController::class, 'kanban']);

Route::get('/login', [LoginController::class, 'mostrarFormLogin']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);

Route::get('/contacto', [LeadController::class, 'mostrarFormContacto']);
Route::post('/contacto', [LeadController::class, 'nuevoContacto']);

Route::get('/leads/nuevo', [LeadController::class, 'mostrarFormLead']);
Route::post('/leads/guardar', [LeadController::class, 'nuevoLead']);

Route::post('/leads/cambiar-estado/{id}', [LeadController::class, 'cambiarEstado']);
Route::post('/pipeline/cambiar-estado', [LeadController::class, 'cambiarEstadoKanban']);

Route::post('/leads/{id}/actualizar', [LeadController::class, 'actualizarLead']);
Route::post('/leads/{id}/eliminar', [LeadController::class, 'eliminarLead']);

Route::post('/leads/{id}/notas/guardar', [LeadController::class, 'nuevaNota']);
Route::get('/leads/{id}', [LeadController::class, 'mostrarDetalle']);

Route::handleRoute();