<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\Traits\ApiResponse;

class CheckRole
{
    use ApiResponse;

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if ($user->roles()->whereIn('name', $roles)->exists()) {
            return $next($request);
        }

        return $this->errorResponse('Unauthorized',403);
    }
}
