<?php

namespace Hamzasgd\LaravelOps\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    /**
     * Return a success JSON response.
     *
     * @param array $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function success($data = [], string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param array $data
     * @return JsonResponse
     */
    protected function error(string $message = 'An error occurred', int $code = 400, array $data = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $code);
    }
} 