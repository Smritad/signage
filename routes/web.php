<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\DashboardController;

// Frontend routes
Route::get('/', function () {
    return view('welcome');
});




// Backend Routes
Route::get('/admin-login', [LoginController::class, 'login'])->name('admin.login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('admin.authenticate');
Route::get('/admin-logout', [LoginController::class, 'logout'])->name('admin.logout');
Route::get('/change-password', [LoginController::class, 'change_password'])->name('admin.changepassword');
Route::post('/update-password', [LoginController::class, 'updatePassword'])->name('admin.updatepassword');

Route::get('/admin-register', [LoginController::class, 'register'])->name('admin.register');
Route::post('/register', [LoginController::class, 'authenticate_register'])->name('admin.register.authenticate');
    
// // Admin Routes with Middleware
Route::group(['middleware' => ['auth:web', \App\Http\Middleware\PreventBackHistoryMiddleware::class]], function () {
        Route::get('/dashboard', function () {
            return view('backend.dashboard'); 
        })->name('admin.dashboard');
});