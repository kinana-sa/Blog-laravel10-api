<?php

namespace App\Http\Controllers\Api\Traits;

trait ApiResponse
{

    public function successResponse($data = null, $message = null, $code = null)
    {
        return response()->json([
            'data' => $data,
            'status' => true,
            'message' => $message

        ], $code);
    }

    public function errorResponse($message = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message

        ], $code);
    }
}