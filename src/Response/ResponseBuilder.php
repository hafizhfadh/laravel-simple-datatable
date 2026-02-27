<?php

namespace HafizhFadh\LaravelSimpleDatatable\Response;

use Illuminate\Http\JsonResponse;

class ResponseBuilder
{
    public static function success(array $data): JsonResponse
    {
        return response()->json($data);
    }
}
