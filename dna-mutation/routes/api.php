<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MutationController;

Route::post('/mutation',[MutationController::class, 'mutation']);

Route::get('/stats',[MutationController::class, 'stats']);

Route::get('/list',[MutationController::class, 'list']);

