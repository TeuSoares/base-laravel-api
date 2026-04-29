<?php

namespace App\Core\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseApiException extends HttpException
{
    protected array|string|null $details;

    public function __construct(string $message, array|string|null $details = null, int $statusCode = 400)
    {
        $this->details = $details;
        parent::__construct($statusCode, $message);
    }

    public function getDetails(): array|string|null
    {
        return $this->details;
    }
}
