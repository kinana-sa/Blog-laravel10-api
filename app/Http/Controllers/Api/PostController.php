<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
use App\Models\Image;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Controllers\Api\Traits\FileUploadTrait;

class PostController extends Controller
{
    use ApiResponse, FileUploadTrait;

    public function index()
    {
        $posts = Post::with('user', 'comments.user', 'images')->get();
        $data = PostResource::collection($posts);
        return $this->successResponse($data, "show all Posts", 200);
    }

    public function store(PostRequest $request)
    {
        try {
            $post = Post::create([
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => Auth::id()
            ]);

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach($images as $img){

                    $image = $this->uploadFile($img, "posts/images/");
                    
                    $post->images()->save($image);
                }
            }
            if ($request->hasFile('videos')) {
                $videos = $request->file('videos');
                foreach($videos as $vid){

                    $video = $this->uploadFile($vid, "posts/videos/");
                    
                    $post->videos()->save($video);
                }
            }
            return $this->successResponse(new PostResource($post), "Create New Post.", 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(Post $post)
    {
        return $this->successResponse(new PostResource($post), "Show Post.", 200);
    }

    public function update(PostRequest $request, Post $post)
    {
        try {
            $this->authorize('update', $post);
            $post->update([
                'title' => $request->title,
                'body' => $request->body
            ]);
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach($images as $img){

                    $image = $this->uploadFile($img, "posts/images/");
                    
                    $post->images()->save($image);
                }
            }
            if ($request->hasFile('videos')) {
                $videos = $request->file('videos');
                foreach($videos as $vid){

                    $video = $this->uploadFile($vid, "posts/videos/");
                    
                    $post->videos()->save($video);
                }
            }
            return $this->successResponse(new PostResource($post), "Post Updated Successfully.", 200);
        } catch (AuthorizationException) {
            return $this->errorResponse('Unauthorized', 403);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to Update Post.', 500);
        }
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->user()->id)
        {
            return $this->errorResponse('Unauthorized', 403);
        }
        $post->delete();
        return $this->successResponse(null, "Post Deleted Successfully.", 200);
    }

    public function restore($id)
    {
        $user = Auth::user();
        $post = Post::onlyTrashed()->find($id);
        if ($user->id !== $post->user_id) 
        {
            return $this->errorResponse('You Do Not Have Permission For This Action.',403);
        }
        if (!$post)
        {
            return $this->errorResponse('Post Not Found In Deleted Posts', 404);
        }
        $post->restore();
        return $this->successResponse(new PostResource($post), "Post Restored Successfully", 200);
    }

    public function restoreAll()
    {
        $user = Auth::user();
        $trashedPosts = Post::onlyTrashed()->where('user_id',$user->id)->get();
        if (!$trashedPosts->isEmpty()) {
            $trashedPosts->each(function ($post) {
                $post->restore();
            });
            return $this->successResponse($trashedPosts, " All Posts Restored Successfully", 200);
        } else {
            return $this->errorResponse('There Is No Posts To Restore.', 404);
        }
    }
    //APIs
    public function userPosts(User $user)
    {
        $posts = $user->posts()->get();
        if ($posts->isEmpty()) {
            return $this->errorResponse('User Does Not Have Posts', 404);
        }
        return $this->successResponse(PostResource::collection($posts), "Show All User's Posts", 200);
    }

    public function getPostsImages()
    {
        $posts = Post::with('images')->get();
        $data = PostResource::collection($posts);
        return $this->successResponse($data, "show all Posts", 200);
    }

     //Only admin and super admin Can Access the Following Method
     public function deletedPosts()
     {
         $posts = Post::onlyTrashed()->get();
         if (!$posts->isEmpty()) {
             return $this->successResponse($posts, " Show All Deleted Posts", 200);
         } else {
             return $this->errorResponse('There Is No Deleted Posts To Show.', 404);
         }
     }
 
     public function deletedPostsByUser()
     {
         $user = Auth::user();
         $posts = Post::onlyTrashed()->where('user_id', $user->id)->get();
         if (!$posts->isEmpty()) {
             return $this->successResponse($posts, "Show All Deleted Posts", 200);
         } else {
             return $this->errorResponse('There Is No Deleted Posts To Show.', 404);
         }
     }
}
