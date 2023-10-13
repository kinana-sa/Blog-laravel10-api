<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;

class UserController extends Controller
{ 
    use ApiResponse;

    public function index()
    {
        $users = User::all();
        return $this->successResponse($users, "Show All Users", 200);
    }

    public function usersWithRoles()
    {
        $users = User::with('roles')
            ->whereHas('roles')->get();
        if ($users) {
            return $this->successResponse($users, "Show All Users With Roles", 200);
        }
        return $this->errorResponse('There Is No Role Added To User Yet', 400);
    }
}