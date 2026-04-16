<?php

namespace App\Core\Abstracts;

use App\Core\Support\ThrowApi;

abstract class Action
{
    protected function error(): ThrowApi
    {
        return throwApi();
    }
}
