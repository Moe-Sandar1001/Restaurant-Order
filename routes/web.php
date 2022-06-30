<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DishController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// kitchen panel
Route::resource('/dish', App\Http\Controllers\DishesController::class);
Route::get('/order', [App\Http\Controllers\DishesController::class, 'order'])->name('kitchen.order');

Route::get('/order/{order}/approve', [App\Http\Controllers\DishesController::class, 'approve']);
Route::get('/order/{order}/cancel', [App\Http\Controllers\DishesController::class, 'cancel']);
Route::get('/order/{order}/ready', [App\Http\Controllers\DishesController::class, 'ready']);
Route::get('/order/{order}/done', [App\Http\Controllers\DishesController::class, 'done']);

// waiter panel
Route::get('/', [App\Http\Controllers\OrderController::class, 'index'])->name('order.form');
Route::post('/order_submit', [App\Http\Controllers\OrderController::class, 'submit'])->name('order.submit');
Route::get('/order/{order}/serve', [App\Http\Controllers\OrderController::class, 'serve']);


// Auth::routes();

// auth route false in laravel
Auth::routes([
  'register' => false, // Registration Routes...
  'reset' => false, // Password Reset Routes...
  'verify' => false, // Email Verification Routes...
]);

// Route::get('/home', [App\Http\Controllers\OrderController::class, 'index'])->name('home');


