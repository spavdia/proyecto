<?php

use Sergio\Lib\Route;
use Sergio\App\controllers\HomeController;


Route::get('/', [HomeController::class,'index']);


Route::handleRoute();
