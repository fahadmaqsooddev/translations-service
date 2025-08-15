<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\AuthController;


Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('translations', TranslationController::class);
    Route::get('/translations/search', [TranslationController::class, 'search']);
    Route::get('/translations/export/json', [TranslationController::class, 'exportJson']);
    //Locales Route
    Route::apiResource('locales', LocaleController::class);
    //Tags Route 
    Route::apiResource('tags', TagController::class);
});


