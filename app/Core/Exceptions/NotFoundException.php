<?php

namespace App\Core\Exceptions;

class NotFoundException extends BaseApiException
{
    public function __construct(string $message = 'Resource not found')
    {
        parent::__construct(['not_found' => $message], 404);
    }
}
