<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;
use App\Http\Controllers\Api\Traits\ApiResponse;

class CommentController extends Controller
{

    use ApiResponse;

    public function index()
    {
        $comments = Comment::all();
        $data = CommentResource::collection($comments);
        return $this->successResponse($data, "Show All Comments", 200);
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

    public function store(Request $request, $id)
    {
        try {
            Comment::create([
                'content' => $request->content,
                'user_id' => Auth::id(),
                'post_id' => $id
            ]);
            return $this->successResponse($request->content, "Create New Comment", 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed To Create Comment.', 500);
        }
    }

    public function show(Comment $comment)
    {
        return $this->successResponse($comment, "Show Comment", 200);
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id != Auth::id()) {
            return $this->errorResponse('Unauthorized', 403);
        }
        try {
            $comment->update([
                'content' => $request->content
            ]);
            return $this->successResponse($comment, "Comment Updated Successfully.", 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed To Update Comment.', 500);
        }
    }

    public function destroy(Comment $comment)
    {   //Only Comment Owner or Admins Can Delete a Comment
        if ($comment->user_id == Auth::id() || User::findOrFail(Auth::id())->roles()->where('name', 'admin')->exists()) {
            $comment->delete();
            return $this->successResponse(null, "Comment Deleted Successfully.", 200);
        }
        return $this->errorResponse('Unauthorized', 403);
    }

    public function restore($id)
    {
        $comment = Comment::onlyTrashed()->find($id);
        if (!$comment) {
            return $this->errorResponse('Comment Does Not Exists', 404);
        }
        if ($comment->user_id == Auth::id()) {
            $comment->restore();
            return $this->successResponse($comment, "Post Restored Successfully", 200);
        } else {
            return $this->errorResponse('Unauthorized', 403);
        }
    }
}
