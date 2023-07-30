<?php

use App\Http\Controllers\API\ApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::post('loginApi/{guard}', [ApiController::class, 'login']);


Route::post('registerApi/{guard}', [ApiController::class, 'register']);


Route::post('logoutApi/{guard}', [ApiController::class, 'logout'])->middleware(['auth:api,admin,vendor,client','client']);


Route::get('profileApi/{guard}', [ApiController::class, 'getProfile'])->middleware(['auth:api,admin,vendor,client','client']);


Route::post('forgot-password-api', [ApiController::class, 'forgotPassword']);


Route::view('reset-password-view', 'reset-password')->name('password.reset.view');

Route::post('update-password-api', [ApiController::class, 'resetPassword'])->name('password.update.api');
