<?php

namespace App\Core\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseApiException extends HttpException
{
    protected array $payload;

    public function __construct(array|string $message, int $statusCode = 400)
    {
        $this->payload = is_array($message) ? $message : ['message' => $message];
        parent::__construct($statusCode, is_array($message) ? 'API Error' : $message);
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
