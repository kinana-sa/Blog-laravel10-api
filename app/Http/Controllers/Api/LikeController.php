<?php

namespace App\Http\Controllers\Api;

use App\Models\Like;
use App\Models\Post;
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
                    ->where('likeable_type', $likeableType)
                    ->where('likeable_id', $likeableId)
                    ->first();
    
        if ($like) {
            $like->delete();
            return $this->successResponse(null, 'Like Deleted Successfully.');
    
        } else {
            Like::create([
                'user_id' => auth()->id(),
                'likeable_type' => $likeableType,
                'likeable_id' => $likeableId,
            ]);
        }
        return $this->successResponse(null, 'Like Added Successfully.');
    }

    public function getLikedPosts()
    {
        $user = Auth::user();
        $likedPosts = $user->likes()->where('likeable_type',Post::class)->with('likeable')->get();
        return $this->successResponse(PostResource::collection($likedPosts), 'Show Liked Posts Successfully');
    }
    
}