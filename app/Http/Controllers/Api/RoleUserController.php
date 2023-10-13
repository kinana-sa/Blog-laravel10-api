<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;

class RoleUserController extends Controller
{
    use ApiResponse;

    public function addRole(Request $request, User $user)
    {
        $role = Role::where('name', $request->role_name)->first();
        if (!$role) {
            return $this->errorResponse('Role Not Found', 404);
        }
        if ($user->roles()->where('name', $role->name)->exists()) {
            return $this->errorResponse('User Already Has This Role', 409);
        }
        try {
            $user->roles()->attach($role);
            return $this->successResponse(["user" => $user->name, "role" => $role->name], "Role Added Seccessfuly", 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed To Add The Role To The User', 500);
        }
    }

    public function deleteRole(Request $request,  User $user)
    {
        $role = Role::where('name', $request->role_name)->first();
        if (!$role) {
            return $this->errorResponse('Role Not Found', 404);
        }
        if (!$user->roles()->where('name', $role->name)->exists()) {
            return $this->errorResponse('User Does Not Have This Role', 409);
        }
        try {
            $user->roles()->detach($role);
            return $this->successResponse(["user" => $user->name, "role" => $role->name], "Role deleted Seccessfuly", 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed To Delete The User Role.', 500);
        }
    }
}
