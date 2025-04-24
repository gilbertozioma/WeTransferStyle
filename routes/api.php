<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\DownloadController;

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

// Upload endpoint
Route::post('/upload', [UploadController::class, 'store']);

// Download endpoint
Route::get('/download/{token}', [DownloadController::class, 'show']);

// Stats endpoint (bonus feature)
Route::get('/uploads/stats/{token}', [StatsController::class, 'show']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
