<?php

namespace App\Core\Exceptions;

class ForbiddenException extends BaseApiException
{
    public function __construct(string $message = 'Access denied')
    {
        parent::__construct(['forbidden' => $message], 403);
    }
}
