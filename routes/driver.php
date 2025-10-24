<?php

use Illuminate\Support\Facades\Route;

// Rutas específicas para el panel conductor
Route::domain('conductor.' . parse_url(config('app.url'), PHP_URL_HOST))->group(function () {
    Route::redirect('/', '/conductor');
});

// Rutas alternativas con puerto diferente
Route::group(['prefix' => 'driver-panel'], function () {
    Route::redirect('/', '/conductor');
});