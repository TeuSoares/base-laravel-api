<?php

namespace App\Core\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;

class ResponseBuilder
{
    public function data(mixed $data, int $status = 200, ?string $message = null): JsonResponse
    {
        if ($data instanceof JsonResource || $data instanceof AnonymousResourceCollection) {
            $data = $data->response()->getData(true);
        }

        return self::build([
            'data' => $data ?? [],
            'message' => $message,
        ], $status);
    }

    public function message(string $message, int $status = 200): JsonResponse
    {
        return self::build(['message' => $message], $status);
    }

    public function error(array|string $error, int $status = 400, ?string $message = null): JsonResponse
    {
        if (is_string($error)) {
            $error = ['message' => $error];
        }

        return self::build([
            'message' => $message ?? 'Request failed',
            'errors' => $error
        ], $status);
    }

    public function paginated(AbstractPaginator $pagination, int $status = 200): JsonResponse
    {
        $data = $pagination->toArray();

        return self::build([
            'data' => $data['data'] ?? [],
            'meta' => [
                'current_page' => $data['current_page'] ?? null,
                'per_page'     => $data['per_page'] ?? null,
                'total'        => $data['total'] ?? null,
                'last_page'    => $data['last_page'] ?? null,
                'from'         => $data['from'] ?? null,
                'to'           => $data['to'] ?? null,
            ],
        ], $status);
    }

    public function noContent(): Response
    {
        return response()->noContent();
    }

    protected function build(array $payload, int $status = 200): JsonResponse
    {
        $clean = Arr::where($payload, function ($value, $key) {
            if ($key === 'data') return true;
            return !is_null($value) && $value !== [];
        });

        return response()->json($clean, $status);
    }
}
