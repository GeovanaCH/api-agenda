<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'API Agenda',
        'health' => url('/api/health'),
    ]);
});
