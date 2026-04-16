<?php

namespace App\Core\Exceptions;

class InternalErrorException extends BaseApiException
{
    public function __construct(string $message = 'Internal server error')
    {
        parent::__construct(['internal' => $message], 500);
    }
}
