<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\Traits\ApiResponse;

class FollowController extends Controller
{
    use ApiResponse;

    public function follow(User $userToFollow)
    {
        Auth::user()->followings()->attach($userToFollow->id); // To follow a user

        return $this->successResponse(null, 'Follow Request Sended Successfully.', 201);
    }

    public function unfollow(User $userToUnfollow)
    {
        Auth::user()->followings()->detach($userToUnfollow->id); // To unfollow a user

        return $this->successResponse(null, 'Follow Deleted Successfully.', 200);
    }

    public function accept(User $user)
    {
        auth()->user()->followers()->where('follower_id', $user->id)->update(['status' => 'accepted']);
    }

    public function reject(User $user)
    {
        auth()->user()->followers()->where('follower_id', $user->id)->update(['status' => 'rejected']);
    }

    public function followersCount(User $user)
    {
        return $user->followers()->where('status', 'accepted')->count();
    }

}
