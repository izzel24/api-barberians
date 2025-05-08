<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\merchantsController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Middleware;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:api', 'role:admin,merchant'])->group(function () {
Route::get('/merchants', [merchantsController::class, 'fetch']);
Route::get('/users', [UserController::class, 'fetch']);
Route::put('/user/{id}', [UserController::class, 'update']);  
Route::delete('/user/{id}', [UserController::class, 'destroy']);
Route::put('/merchants/{id}/status', [merchantsController::class, 'updateStatus']);
});

Route::middleware(['auth:api', 'role:merchant'])->post('/merchants', [merchantsController::class, 'create']);

