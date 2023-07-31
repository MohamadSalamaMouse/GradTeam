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
Route::get('members', [ShowController::class,'viewTeamMembers'])->middleware('auth:sanctum');
Route::post('StoreTeam', [ShowController::class, 'StoreTeam'])->middleware('auth:sanctum');
Route::post('deleteTeam', [ShowController::class, 'deleteTeam'])->middleware('auth:sanctum');
Route::post('JoinTeam', [ShowController::class, 'JoinTeam'])->middleware('auth:sanctum');
Route::get('requests', [ShowController::class, 'requests'])->middleware('auth:sanctum');
Route::get('AcceptJoin', [ShowController::class, 'AcceptJoin'])->middleware('auth:sanctum');
Route::get('RejectJoin', [ShowController::class, 'RejectJoin'])->middleware('auth:sanctum');
Route::get('findTeam', [ShowController::class, 'findTeam'])->middleware('auth:sanctum');
Route::get('DeleteMemeber', [ShowController::class, 'DeleteMemeber'])->middleware('auth:sanctum');
//Route::post('CreateMember', [ShowController::class, 'CreateMember']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('emailVerification', [EmailVerificationController::class, 'emailVerification'])->middleware('guest:sanctum');
Route::post('updateTeam', [ShowController::class, 'updateTeam'])->middleware('auth:sanctum');
Route::post('leaveTeam', [ShowController::class, 'leaveTeam'])->middleware('auth:sanctum');
Route::post('update-profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
