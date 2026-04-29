<?php

namespace App\Core\Exceptions;

class InternalErrorException extends BaseApiException
{
    public function __construct(
        string $message = 'Internal server error',
        array|string|null $details = null
    ) {
        parent::__construct($message, $details, 500);
    }
}
