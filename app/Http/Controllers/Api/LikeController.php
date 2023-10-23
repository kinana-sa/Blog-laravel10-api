<?php

namespace App\Http\Controllers\Api;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\Traits\ApiResponse;

class LikeController extends Controller
{
    use ApiResponse;

    public function toggleLike(Request $request)
    {
        $likeableType = $request->input('likeable_type');
        $likeableId = $request->input('likeable_id');

        $like = Like::where('user_id', auth()->id())
            ->where('likeable_type', 'App\Models\\' . ucfirst($likeableType))
            ->where('likeable_id', $likeableId)
            ->first();

        if ($like) {
            $like->delete();
            return $this->successResponse(null, 'Like Deleted Successfully.');
        } else {
            if ($likeableType == 'post') {
                $post = Post::find($likeableId);
                $post->likes()->create(['user_id' => auth()->id()]);
            } elseif ($likeableType == 'comment') {
                $comment = Comment::find($likeableId);
                $comment->likes()->create(['user_id' => auth()->id()]);
            }
            // Like::create([
            //     'user_id' => auth()->id(),
            //     'likeable_type' => $likeableType,
            //     'likeable_id' => $likeableId,
            // ]);
        }
        return $this->successResponse(null, 'Like Added Successfully.');
    }

    public function getLikedPosts()
    {
         $likedPosts = Auth::user()->likes()
            ->where('likeable_type', Post::class)
            ->with('likeable')
            ->get()
            ->pluck('likeable');

        // Another Way to Get Liked Posts of Auth User
        //$userId = Auth::id();
        // $likedPosts = Post::whereHas('likes', function ($query) use ($userId) {
        //     $query->where('user_id', $userId);
        // })->get();
        return $this->successResponse($likedPosts, 'Show Liked Posts Successfully');
    }
}
