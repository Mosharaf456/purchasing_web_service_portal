<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route::prefix('api')->group(function () {
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::get('/index', [AuthController::class, 'index']);
// });

// Remember that routes declared in api.php will automatically prepend the /api prefix, e.g.:


Route::prefix('v1')->group(function() {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/index', [AuthController::class, 'index']);
});

// // JWT Routes (First-party)
// Route::prefix('auth')->group(function () {
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::post('/refresh', [AuthController::class, 'refresh']);
//     Route::get('/me', [AuthController::class, 'me']);
// });

// // OAuth2 Routes (Third-party)
// Route::prefix('oauth')->group(function () {
//     Route::post('/login', [AuthController::class, 'oauthLogin']);
//     Route::post('/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
// });

// // Protected Example Route
// Route::middleware('auth:api')->get('/protected', function () {
//     return response()->json(['message' => 'JWT Authenticated']);
// });

// Route::middleware('auth:oauth')->get('/oauth-protected', function () {
//     return response()->json(['message' => 'OAuth Authenticated']);
// });

// Route::prefix('api')->group(function () {
//     Route::post('/login', [AuthController::class, 'login']);
// });


// Route::middleware(['auth:api'])->group(function () {
//     Route::get('/user', fn(Request $request) => $request->user());
//     Route::post('/logout', [AuthController::class, 'logout']);
// });

// OAuth2 Routes
// Route::prefix('oauth')->group(function () {
//     Route::post('/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')
//          ->middleware('throttle:5,1');
// });





////////////////use App\Http\Controllers\AuthController;


// Route::post('register', [AuthController::class, 'register']);
// Route::post('login', [AuthController::class, 'login']);
// Route::post('refresh', [AuthController::class, 'refresh']);

// Route::middleware('auth:api')->group(function () {
//     Route::post('logout', [AuthController::class, 'logout']);
// });



