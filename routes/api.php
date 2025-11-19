<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json("Larvael Todo Backend working");
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

        Route::get('/todos', [TodoController::class, 'all']);
        Route::post('/todos', [TodoController::class, 'create']);
        Route::get('/todos/{id}', [TodoController::class, 'get']);
        Route::put('/todos/{id}', [TodoController::class, 'update']);
        Route::delete('/todos/{id}', [TodoController::class, 'delete']);
    });
});
