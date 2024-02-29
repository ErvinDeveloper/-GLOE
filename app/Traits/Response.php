<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait Response
{
    public function responseSuccess(array $data = []): JsonResponse
    {
        return response()->json(
            collect(['success' => true])->merge($data)
        );
    }

    public function responseError(array $data = []): JsonResponse
    {
        return response()->json(
            collect(['error' => true])->merge($data)
        );
    }
}
