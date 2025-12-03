<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WatchListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware('check.access.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function () {
        return auth()->user();
    });
});

// Route::prefix('watchlist')->middleware('check.access.token')->group(function () {
Route::prefix('watchlist')->group(function () {
    Route::get('/{userId}', [WatchlistController::class, 'index']);
    Route::post('/add', [WatchlistController::class, 'add']);
    Route::put('/update/{id}', [WatchlistController::class, 'update']);
    Route::delete('/delete/{id}', [WatchlistController::class, 'delete']);
    Route::post('/toggle', [WatchlistController::class, 'toggle']);
});
