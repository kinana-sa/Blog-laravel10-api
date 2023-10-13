<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\RoleUserController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function(){

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::apiResource('/posts', PostController::class);

Route::prefix('posts')->middleware('auth:sanctum')->group(function(){

    Route::post('/likes/toggle', [LikeController::class, 'toggleLike'])->name('likes.toggle-like');
    Route::get('/user_posts/{user_id}',[PostController::class,'userPosts']);
    
    Route::get('/user_del_posts',[PostController::class,'deletedPostsByUser']);
    Route::patch('/restore/{post}',[PostController::class,'restore']);
    Route::patch('/restoreall',[PostController::class,'restoreAll']);

});

Route::get('/postsImages',[PostController::class,'getPostsImages'])->middleware('auth:sanctum');

Route::prefix('admin')->middleware(['auth:sanctum','role:admin,super_admin'])->group(function(){

    Route::post('/addRole/{user}',[RoleUserController::class,'addRole']);
    Route::post('/deleteRole/{user}',[RoleUserController::class,'deleteRole']);
    Route::get('/usersRoles',[UserController::class,'usersWithRoles']);
    Route::get('/allUsers',[UserController::class,'index']);
    
    Route::get('/soft_del_posts',[PostController::class,'deletedPosts']);
    
    Route::get('/users/{user}/comments',[CommentController::class,'getUserComments']);

});

Route::prefix('comments')->middleware('auth:sanctum')->group(function(){

    Route::apiResource('', CommentController::class);
    Route::post('/likes/toggle', [LikeController::class, 'toggleLike'])->name('likes.toggle-like');

    Route::get('/{post}',[CommentController::class,'getPostComments']);
   
});
