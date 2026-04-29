<?php

namespace App\Core\Exceptions;

class GenericHttpException extends BaseApiException
{
    public function __construct(
        string $message,
        int $statusCode = 400,
        array|string|null $details = null
    ) {
        parent::__construct($message, $details, $statusCode);
    }
}
