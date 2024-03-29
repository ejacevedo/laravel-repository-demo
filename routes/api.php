<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\RouteController;
use Illuminate\Http\Response;


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

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/addresses', [AddressController::class, 'index']);

    Route::get('/routes', [RouteController::class, 'index']);
    Route::post('/routes/quote', [RouteController::class, 'quote']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change_password', [AuthController::class, 'changePassword']);

    Route::get('/google_map_key', function () {
        $google_map_key = config('services.google_map.key');
        return response()->json( ['key' => $google_map_key ], $google_map_key ? Response::HTTP_OK :  Response::HTTP_BAD_REQUEST);
    });

});

Route::group(['middleware' => ['auth:sanctum', 'role:' . implode('|', [Roles::ROOT, Roles::ADMIN_QUOTE])]], function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::patch('/users/{username}/roles', [UserController::class, 'updateRoles']);
    Route::get('/roles', [UserController::class, 'getRoles']);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint not found. If the error persists, contact your system team'
    ], 404);
});
