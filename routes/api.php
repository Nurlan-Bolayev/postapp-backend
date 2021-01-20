<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post('/register', UserController::class . '@register');
    Route::post('/login', UserController::class . '@login');
    Route::post('/posts/create');

    Route::post('/posts/create', PostController::class . '@create');
    Route::put('/posts/{post}', PostController::class . '@update');
    Route::delete('/posts/{post}', PostController::class . '@delete');

    Route::post('/posts/{post}/comments/create', CommentController::class . '@create');
    Route::put('/posts/comments/{comment}', CommentController::class . '@update');
    Route::delete('/posts/comments/{comment}', CommentController::class . '@delete');

    Route::post('/comments/{comment}/reply' , CommentController::class .'@replyTo');
    Route::put('/comments/{comment}/reply' , CommentController::class .'@editReply');
    Route::delete('/comments/{comment}/reply' , CommentController::class .'@deleteReply');
