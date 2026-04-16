<?php

namespace App\Core\Support;

use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\GenericHttpException;
use App\Core\Exceptions\InternalErrorException;
use App\Core\Exceptions\NotFoundException;
use Illuminate\Validation\ValidationException;

class ThrowApi
{
    public function validation(array $errors): never
    {
        throw ValidationException::withMessages($errors);
    }

    public function notFound(?string $message = null): never
    {
        $message ??= __('exceptions.not_found');
        throw new NotFoundException($message);
    }

    public function forbidden(?string $message = null): never
    {
        $message ??= __('exceptions.forbidden');
        throw new ForbiddenException($message);
    }

    public function http(array|string $message, int $status = 400): never
    {
        throw new GenericHttpException($message, $status);
    }

    public function internal(?string $message = null): never
    {
        $message ??= __('exceptions.internal_error');
        throw new InternalErrorException($message);
    }
}
