<?php

use App\Http\Controllers\LayerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('map');
});

// API pública para retornar camadas em GeoJSON
Route::get('/api/layers', [LayerController::class, 'index']);
