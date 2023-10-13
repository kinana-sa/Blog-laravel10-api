<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\RoleUserController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::apiResource('posts',PostController::class)->middleware(['auth:sanctum','admin']);

//SHOW ALL POSTS ADMIN ONLY
Route::get('/posts',[PostController::class,'index'])->middleware(['auth:sanctum','role:admin,super_admin']);

// CREATE POST
Route::post('/posts',[PostController::class,'store'])->middleware('auth:sanctum');

//SHOW ONE POST
Route::get('/posts/{post}',[PostController::class,'show'])->middleware('auth:sanctum');

//UPDATE POST
Route::put('/posts/{post}',[PostController::class,'update'])->middleware('auth:sanctum');

//DELETE POST
Route::delete('/posts/{post}',[PostController::class,'destroy'])->middleware('auth:sanctum');

//SHOW ALL DELETED POSTS ADMIN ONLY
Route::get('/deleted',[PostController::class,'showDeleted'])->middleware(['auth:sanctum','role:admin,editor']);

// RESTORE ONE POST ADMIN ONLY
Route::patch('/posts/restore/{post}',[PostController::class,'restore'])->middleware(['auth:sanctum','role:admin,editor']);

// RESTORE ALL POSTS ADMIN ONLY
Route::patch('/restoreall',[PostController::class,'restoreAll'])->middleware(['auth:sanctum','role:admin,editor']);

Route::post('/addRole/{user_id}',[RoleUserController::class,'addRole'])->middleware(['auth:sanctum','role:admin,super_admin']);
Route::post('/deleteRole/{user_id}',[RoleUserController::class,'deleteRole'])->middleware(['auth:sanctum','role:admin,super_admin']);
Route::get('/usersRoles',[UserController::class,'usersWithRoles'])->middleware(['auth:sanctum','role:admin,super_admin']);

Route::post('register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//USER POSTS
Route::get('/user_posts/{user_id}',[PostController::class,'userPosts'])->middleware('auth:sanctum');

//COMMENT ROUTES
Route::get('/comments',[CommentController::class,'index'])->middleware('auth:sanctum');
Route::post('/comments/{id}',[CommentController::class,'store'])->middleware('auth:sanctum');
Route::put('/comments/{id}',[CommentController::class,'update'])->middleware('auth:sanctum');
Route::delete('/comments/{id}',[CommentController::class,'destroy'])->middleware('auth:sanctum');
Route::get('/comments/{post}',[CommentController::class,'getPostComments'])->middleware('auth:sanctum');

////////////////////////////////////////////////
///////////////////////////////////////////////
Route::prefix('posts')->middleware('auth:sanctum')->group(function(){

    // Route::get('/e']);
    // Route::delete('/{post}',[PostController::class,'destroy']);
    // Route::post('/{post}/like', [LikeController::class, 'likePost'])->name('posts.like');
    // Route::delete('/{post}/unlike', [LikeController::class, 'unlikePost'])->name('posts.unlike');',[PostController::class,'index']);
    // Route::get('/{post}',[PostController::class,'show']);
    // Route::post('/',[PostController::class,'store']);
    // Route::put('/{post}',[PostController::class,'updat
});