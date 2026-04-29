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

    public function error(string $message, array|null $error = null, int $status = 400): JsonResponse
    {
        $data = [
            'message' => $message,
        ];

        if ($error !== null && $error !== []) {
            $data['errors'] = array_map(function ($value) {
                return is_array($value) ? $value : [$value];
            }, $error);
        }

        return self::build($data, $status);
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
