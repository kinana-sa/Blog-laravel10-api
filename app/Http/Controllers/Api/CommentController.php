<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
use App\Models\Image;
use App\Models\Video;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Events\CommentDeleting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Api\Traits\FileUploadTrait;

class CommentController extends Controller
{

    use ApiResponse, FileUploadTrait;

    public function index()
    {
        $comments = Comment::all();
        $data = CommentResource::collection($comments);
        return $this->successResponse($data, "Show All Comments", 200);
    }

    public function store(CommentRequest $request, Post $post)
    {
        try {
            $comment = Comment::create([
                'content' => $request->content,
                'user_id' => Auth::id(),
                'post_id' => $post->id
            ]);
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image = $this->uploadFile($image, "comments/images/");
                $comment->image()->save($image);
            }
            elseif ($request->hasFile('video')) {
                $video = $request->file('video');
                $video = $this->uploadFile($video, "comments/videos/");
               
                $comment->video()->save($video);
            }
            return $this->successResponse(new CommentResource($comment), "Create New Comment", 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed To Create Comment.', 500);
        }
    }

    public function show(Comment $comment)
    {
        return $this->successResponse(new CommentResource($comment), "Show Comment", 200);
    }

    public function update(CommentRequest $request, Comment $comment)
    {
        if ($comment->user_id != Auth::id()) {
            return $this->errorResponse('Unauthorized', 403);
        }
        
        try {
            $comment->update([
                'content' => $request->content
            ]);
            if ($request->hasFile('image')) {
                [$name, $url] = $this->uploadFile($request, "image", "comments/images/");
                $image = new Image();
                $image->name = $name;
                $image->url = $url;
                $comment->image()->save($image);
            }
            if ($request->hasFile('video')) {
                [$name, $url] = $this->uploadFile($request, "video", "comments/videos/");
                $video = new Video();
                $video->name = $name;
                $video->url = $url;
                $comment->video()->save($video);
            }
            
            return $this->successResponse($comment, "Comment Updated Successfully.", 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed To Update Comment.'.$e, 500);
        }
    }

    public function destroy(Comment $comment)
    {   //Only Comment Owner or Admins Can Delete a Comment
        if ($comment->user_id == Auth::id() || User::findOrFail(Auth::id())->roles()->where('name', 'admin')->exists()) {
           
           event(new CommentDeleting($comment));
            $comment->delete();
            return $this->successResponse(null, "Comment Deleted Successfully.", 200);
        }
        return $this->errorResponse('Unauthorized', 403);
    }

    //APIs
    public function getUserComments(User $user)
    {
        $comments = $user->comments();
        if ($comments) {
            $data = CommentResource::collection($comments);
            return $this->successResponse($data, "Show All User's Comments", 200);
        } else {
            return $this->errorResponse('There Is No Comments yet.', 400);
        }
    }

    public function getPostComments(Post $post)
    {
        if ($post) {
            $comments = $post->comments;

            if (!$comments->isEmpty()) {
                $data = CommentResource::collection($comments);
                return $this->successResponse($data, "Show All Comments", 200);
            } else {
                return $this->errorResponse('There Is No Comments Yet.', 400);
            }
        }
    }
}
