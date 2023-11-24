<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::middleware('can:isMasterUser')->group(function () {
        Route::prefix('/users')->group(function () {
            Route::post('/', [UserController::class, 'store']);
        });
    });
    Route::prefix('/articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index']);
        Route::post('/', [ArticleController::class, 'bulkUpsert']);
    });
    Route::prefix('/logs')->group(function () {
        Route::get('/', [LogController::class, 'index']);
        Route::post('/', [LogController::class, 'store']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
