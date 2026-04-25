<?php

declare(strict_types=1);

use Sergio\Lib\Route;
use Sergio\App\controllers\HomeController;
use Sergio\App\controllers\LoginController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/login', [LoginController::class, 'mostrarLoginForm']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);
Route::get('/panel', [HomeController::class, 'panel']);

Route::handleRoute();
