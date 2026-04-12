<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ManagementController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\SubManagementController;
use App\Http\Controllers\Api\UserController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::prefix('auth')->group(function () {

    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});

Route::middleware('user.auth.api')->group(function () {

    Route::get('/user/profile', [AuthController::class, 'getProfile']);
    Route::post('/user/complete-profile', [AuthController::class, 'completeProfile']);
    Route::get('/managements', [ManagementController::class, 'index']);
    Route::get('/managements/{id}', [ManagementController::class, 'show']);
    Route::get('/sub-managements', [ManagementController::class, 'subManagementList']);
    Route::get('/contents', [SubManagementController::class, 'contents']);
    Route::post('/update-profile-image', [UserController::class, 'updateProfileImage']);
    Route::get('/content/{id}', [ModuleController::class, 'show']);
});
