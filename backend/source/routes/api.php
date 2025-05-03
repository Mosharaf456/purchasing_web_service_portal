<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('v1')->group(function() {
    Route::post('/login', [AuthController::class, 'login']);
   
});

Route::middleware('auth:api')->group(function () {
    // Your protected routes
    Route::get('/index', [AuthController::class, 'index']);
});

// // OAuth2 Routes (Third-party)
// Route::prefix('oauth')->group(function () {
//     Route::post('/login', [AuthController::class, 'oauthLogin']);
//     Route::post('/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
// });


