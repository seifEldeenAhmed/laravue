<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//API routes for user and admin authentication
Route::group(['prefix' => 'auth'], function () {

    // User authentication routes
    Route::group(['prefix' => 'user'], function () {
        Route::post('/login', [UserAuthController::class, 'login']);
        Route::post('/register', [UserAuthController::class, 'register']);
        Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
    });
    // Admin authentication routes
    Route::group(['prefix' => 'admin'], function () {
        Route::post('/login', [AdminAuthController::class, 'login']);
        Route::post('/register', [AdminAuthController::class, 'register']);
        Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
    });

});


//API routes for admin management

Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin']], function () {
    Route::get('/admins', [\App\Http\Controllers\AdminController::class, 'index']);
    Route::post('/admins', [\App\Http\Controllers\AdminController::class, 'store']);
    Route::get('/admins/{admin}', [\App\Http\Controllers\AdminController::class, 'show']);
    Route::put('/admins/{admin}', [\App\Http\Controllers\AdminController::class, 'update']);
    Route::delete('/admins/{admin}', [\App\Http\Controllers\AdminController::class, 'destroy']);
});

//Dashboard route for admin
Route::get('/admin/dashboard', \App\Http\Controllers\AdminDashboardController::class);
Route::group(['prefix' => 'posts', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [\App\Http\Controllers\PostController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\PostController::class, 'store']);
    Route::get('/{post}', [\App\Http\Controllers\PostController::class, 'show']);
    Route::put('/{post}', [\App\Http\Controllers\PostController::class, 'update']);
    Route::delete('/{post}', [\App\Http\Controllers\PostController::class, 'destroy']);
});
