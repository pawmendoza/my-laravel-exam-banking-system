<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/balance', [DashboardController::class, 'getBalance'])->name('api.balance');
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
