<?php

namespace App;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
  //
  protected function successResponse($data = null, string $message = 'Data Ditemukan', int $code = 200): JsonResponse
  {

    if ($data instanceof ResourceCollection) {
      $data = $data->response()->getData(true);
      return response()->json(
        [
          'success' => true,
          'message' => $message,
          'data' => $data['data'],
          'meta' => $data['meta'] ?? null,
        ],
        $code
      );
    }

    if ($data === null) {
      return response()->json(
        [
          'success' => true,
          'message' => $message,
        ],
        $code
      );
    }

    return response()->json(
      [
        'success' => true,
        'message' => $message,
        'data' => $data
      ],
      $code
    );

  }

  protected function errorResponse(string $message, int $code = 404): JsonResponse {
    return response()->json([
      'success' => false,
      'message' => $message,
      'data' => []
    ], $code);
  }
}
