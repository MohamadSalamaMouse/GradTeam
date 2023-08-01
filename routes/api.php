<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\ShowController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\TeamController;
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
//auth routes
Route::post('register', [AuthController::class, 'register'])->middleware('guest:sanctum');
Route::post('login', [AuthController::class, 'login'])->middleware('guest:sanctum');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('update-profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
// show routes
Route::get('ShowAll', [ShowController::class, 'ShowAll'])->middleware('auth:sanctum');

//team routes
Route::get('members', [TeamController::class,'viewTeamMembers'])->middleware('auth:sanctum');
Route::post('StoreTeam', [TeamController::class, 'StoreTeam'])->middleware('auth:sanctum');
Route::post('deleteTeam', [TeamController::class, 'deleteTeam'])->middleware('auth:sanctum');
Route::get('requests', [TeamController::class, 'requests'])->middleware('auth:sanctum');
Route::get('AcceptJoin', [TeamController::class, 'AcceptJoin'])->middleware('auth:sanctum');
Route::get('RejectJoin', [TeamController::class, 'RejectJoin'])->middleware('auth:sanctum');
Route::get('findTeam', [TeamController::class, 'findTeam'])->middleware('auth:sanctum');
Route::post('updateTeam', [TeamController::class, 'updateTeam'])->middleware('auth:sanctum');
//Member routes
Route::post('JoinTeam', [MemberController::class, 'JoinTeam'])->middleware('auth:sanctum');
Route::get('DeleteMemeber', [MemberController::class, 'DeleteMemeber'])->middleware('auth:sanctum');
Route::post('leaveTeam', [MemberController::class, 'leaveTeam'])->middleware('auth:sanctum');

//Email Verification
Route::post('emailVerification', [EmailVerificationController::class, 'emailVerification'])->middleware('guest:sanctum');



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



