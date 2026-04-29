<?php

namespace App\Core\Exceptions;

class ForbiddenException extends BaseApiException
{
    public function __construct(
        string $message = 'Access denied',
        array|string|null $details = null
    ) {
        parent::__construct($message, $details, 403);
    }
}
