<?php

namespace App\Core\Exceptions;

class GenericHttpException extends BaseApiException
{
    public function __construct(array|string $message, int $statusCode = 400)
    {
        parent::__construct($message, $statusCode);
    }
}
