<?php

use App\Http\Controllers\PosCashierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/kasir', PosCashierController::class)->name('kasir');
Route::post('/kasir/orders', [PosCashierController::class, 'store'])->name('kasir.orders.store');
