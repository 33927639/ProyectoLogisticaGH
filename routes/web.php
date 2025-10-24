<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Logout completo - cierra todas las sesiones
Route::get('/logout-all', function () {
    // Cerrar sesión del admin
    Auth::guard('web')->logout();
    Auth::guard('admin')->logout();
    
    // Cerrar sesión del conductor
    Auth::guard('driver')->logout();
    
    // Invalidar la sesión
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('/');
})->name('logout.all');

// Logout específico para admin
Route::get('/admin/logout', function () {
    Auth::guard('admin')->logout();
    return redirect('/admin');
})->name('admin.logout');

// Logout específico para conductor
Route::get('/conductor/logout', function () {
    Auth::guard('driver')->logout();
    return redirect('/conductor');
})->name('conductor.logout');
