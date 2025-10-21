<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\http\Controllers\mutationController;

Route::post('/mutation',[mutationController::class, 'mutation']);

Route::post('/stats',[mutationController::class, 'stats']);

Route::get('/list',[mutationController::class, 'list']);

