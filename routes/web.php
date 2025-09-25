<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Mantén este archivo minimalista si usas Filament como panel.
| NO agregues rutas para TopActiveCustomers aquí.
| Filament registra esa página automáticamente desde tu AdminPanelProvider.
*/

// Opción: redirige la raíz al panel de Filament (path por defecto: /admin)
Route::redirect('/', '/admin');

// (Opcional) endpoint sencillo de healthcheck
Route::get('/health', fn () => response()->json(['ok' => true]));
