<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequestController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {

    Route::get('/suggested-request', [RequestController::class, 'suggestedRequest']); 
    // suggested request
    Route::get('/received-request', [RequestController::class, 'receivedRequest']); // Received request
    Route::post('/withdraw-request', [RequestController::class, 'withdrawRequest']); // set withdraw request 


    
    Route::get('/send-request', [RequestController::class, 'sendRequest']); 
    // Send Request
    Route::post('/create-request', [RequestController::class, 'createRequest']); 
    // send request 
    
    
    Route::get('/approved-request', [RequestController::class, 'getApprovedRequest']); // get approved request
    Route::post('/approved-request', [RequestController::class, 'approvedRequest']); // set approved


    Route::get('/mutual-request', [RequestController::class, 'getMutualRequest']); // get approved request
});
