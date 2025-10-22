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
    
    // Cerrar sesión del conductor
    Auth::guard('driver')->logout();
    
    // Invalidar la sesión
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('/');
})->name('logout.all');
