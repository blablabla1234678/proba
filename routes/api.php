<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::resource('/users', UserController::class);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('/posts', PostController::class, ['except' => ['index', 'show', 'getUserPosts']]);
});
Route::resource('/posts', PostController::class, ['only' => ['index', 'show', 'getUserPosts']]);


Route::get('users/{userId}/posts', [PostController::class, 'getUserPosts']);
Route::post('/tokens', [TokenController::class, 'store']);
Route::delete('/tokens/current', [TokenController::class, 'destroy']);
