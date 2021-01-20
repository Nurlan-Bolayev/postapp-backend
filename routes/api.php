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


Route::middleware('auth:api')->group(function () {
    Route::get('/users/me', fn(Request $request) => $request->user());
    Route::get('/posts', PostController::class . '@all');

    Route::post('/posts/create', PostController::class . '@create');
    Route::put('/posts/{post}', PostController::class . '@update');
    Route::delete('/posts/{post}', PostController::class . '@delete');

    Route::get('/posts/{post}/comments', CommentController::class . '@all');
    Route::post('/posts/{post}/comments/create', CommentController::class . '@create');
    Route::put('/posts/comments/{comment}', CommentController::class . '@update');
    Route::delete('/posts/comments/{comment}', CommentController::class . '@delete');

    Route::get('/comments/{comment}/replies', CommentController::class . '@allReplies');
    Route::post('/comments/{comment}/reply', CommentController::class . '@replyTo');
    Route::put('/comments/{comment}/reply', CommentController::class . '@editReply');
    Route::delete('/comments/{comment}/reply', CommentController::class . '@deleteReply');
});
