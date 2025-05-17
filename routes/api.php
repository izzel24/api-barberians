<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MerchantOpreatingHoursController;
use App\Http\Controllers\MerchantPhotoController;
use App\Http\Controllers\merchantsController;
use App\Http\Controllers\MidtransPaymentController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\UserController;
use App\Models\merchants;
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




Route::get('/users', [UserController::class, 'fetch']);
Route::middleware('auth:api')->get('/user', [UserController::class, 'fetchUser']);
Route::get('/customer/merchants', [merchantsController::class, 'fetchForCustomer']);
Route::get('/customer/merchants/{id}', [merchantsController::class, 'showForCustomer']);

Route::middleware(['auth:api', 'role:admin'])->group(function () {
Route::get('/merchants', [merchantsController::class, 'fetch']);

Route::put('/users/{id}', [UserController::class, 'update']);  
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::delete('/merchants/{id}', [merchantsController::class, 'destroyMerchant']); 
Route::put('/merchants/{id}/status', [merchantsController::class, 'updateStatus']);
});

Route::middleware(['auth:api', 'role:merchant'])->group(function () {
    Route::post('/merchants', [merchantsController::class, 'create']);
     Route::get('/merchant/profile', [merchantsController::class, 'showForMerchants']);

     Route::put('/merchants', [merchantsController::class, 'update']);
});




// Merchant ambil semua antrian yang masuk ke dia
Route::middleware('auth:api')->get('/merchant/queues', [QueueController::class, 'merchantQueues']);

// Merchant menerima atau menolak antrian
Route::put('/queues/{id}/status', [QueueController::class, 'updateStatus']);





Route::get('/merchants/{merchant_id}/operating-hours', [merchantsController::class, 'getOperatingHours']);
// Route::middleware( 'auth:api')->get('/merchant/operating-hours', [merchantsController::class, 'getOperatingHoursByAuth']);
Route::get('/queues', function () {
    return \App\Models\Queue::all();
});


Route::middleware(['auth:api'])->group(function () {
    Route::get('/queues', [QueueController::class, 'userQueues']);
    Route::post('/bookings', [QueueController::class, 'store']);
    // Route::delete('/merchant-photo/{id}', [MerchantPhotoController::class, 'destroy']);
});


Route::get('/services', function () {
    return \App\Models\Service::all();
});

Route::middleware('auth:api')->post('/merchant/services', [merchantsController::class, 'storeService']);


Route::post('/merchant-photo', [MerchantPhotoController::class, 'store']);
// Route::middleware('auth:api')->get('/merchant-profile', [MerchantPhotoController::class, 'getMerchantProfile']);
Route::middleware('auth:api')->post('/merchant/profile',[merchantsController::class,'updateprofile']);

Route::middleware('auth:api')->get('/merchant/services', [merchantsController::class, 'getServices']);
Route::middleware('auth:api')->delete('/merchant-photo/{id}', [MerchantPhotoController::class, 'destroy']);
Route::put('/merchant/services/{id}', [merchantsController::class, 'updateService']);
 Route::delete('/merchant/services/{id}', [merchantsController::class, 'deleteService']);


Route::get('/merchants/{id}/services', [merchantsController::class, 'getServicesbyId']);

Route::get('/all-users', [merchantsController::class, 'getAllUsersWithMerchantData']);


Route::middleware(['auth:api'])->group(function () {
    Route::get('/merchant/operating-hours', [MerchantOpreatingHoursController::class, 'index']);
    Route::post('/merchant/operating-hours', [MerchantOpreatingHoursController::class, 'store']);
    Route::put('/merchant/operating-hours/{id}', [MerchantOpreatingHoursController::class, 'update']);
    Route::delete('/merchant/operating-hours/{id}', [MerchantOpreatingHoursController::class, 'destroy']);
});


Route::post('/create-transaction', [MidtransPaymentController::class, 'getSnapToken']);
Route::post('/midtrans/callback', [MidtransPaymentController::class, 'midtransCallback']);

