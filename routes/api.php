<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShowController;
use App\Http\Controllers\Api\EmailVerificationController;
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
Route::post('register', [AuthController::class, 'register'])->middleware('guest:sanctum');
Route::post('login', [AuthController::class, 'login'])->middleware('guest:sanctum');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('ShowAll', [ShowController::class, 'ShowAll'])->middleware('auth:sanctum');
Route::get('members/{id}', [ShowController::class,'viewTeamMembers']);
Route::post('StoreTeam', [ShowController::class, 'StoreTeam']);
Route::post('deleteTeam/{id}', [ShowController::class, 'deleteTeam']);
Route::post('CreateMember', [ShowController::class, 'CreateMember']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('email-verification', [EmailVerificationController::class, 'emailVerification']);
});
