<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\Api\Traits\ApiResponse;

class AuthController extends Controller
{
    
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
         try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken("API TOKEN")->plainTextToken;
            $success['token'] = $token;
            $success['name'] = $user->name;
            return $this->successResponse($success, "User Created Successfully", 200);
        } catch (\Throwable $th) {
            return $this->errorResponse("ERROR " . $th->getMessage(), 500);
        }
    }

    public function login(LoginRequest $request)
    {

        if (!Auth::attempt($request->only('email', 'password'))) {

            return $this->errorResponse("Error. Invalid Email or Password.", 401);
        }
        $user = User::where('email', $request->email)->first();
        $token = $user->createToken("API TOKEN", ['*'], now()->addMinutes(180))->plainTextToken;

        $success['token'] = $token;
        $success['name'] = $user->name;
        return $this->successResponse($success, "User Loged In Successfully", 200);
    }

    public function logout()
    {
        $user = User::findOrFail(Auth::id());
        // $user = Auth::user();
        $user->tokens()->delete();

        Auth::guard('web')->logout();

        return $this->successResponse($user->name, "Loged Out Seccessfuly", 200);

    }
}