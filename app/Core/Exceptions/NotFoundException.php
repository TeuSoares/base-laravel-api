<?php

namespace App\Core\Exceptions;

class NotFoundException extends BaseApiException
{
    public function __construct(
        string $message = 'Resource not found',
        array|string|null $details = null
    ) {
        parent::__construct($message, $details, 404);
    }
}
