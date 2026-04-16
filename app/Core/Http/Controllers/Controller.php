<?php

namespace App\Core\Http\Controllers;

use App\Core\Support\ResponseBuilder;

abstract class Controller
{
    protected function response(): ResponseBuilder
    {
        return responseBuilder();
    }
}
