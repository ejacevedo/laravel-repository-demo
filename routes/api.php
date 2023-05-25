<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\LocationController;


use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\RouteController;

use App\Constants\Roles;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum'] ], function(){
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/addresses', [AddressController::class, 'index']);

    Route::get('/quotes', [QuoteController::class, 'index']);
    Route::post('/quotes/build', [QuoteController::class, 'build']);

    Route::get('/routes', [RouteController::class, 'index']);
    Route::post('/routes/quote', [RouteController::class, 'quote']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change_password', [AuthController::class, 'changePassword']);
});

Route::group(['middleware' => ['auth:sanctum', 'role:'.implode('|', [Roles::ROOT, Roles::ADMIN_QUOTE])]], function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users2', [UserController::class, 'new_index']);
    Route::patch('/users/{id}/roles', [UserController::class, 'updateRoles']);
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Endpoint not found. If the error persists, contact your system team'], 404);
});
