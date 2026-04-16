<?php

use App\Core\Support\ResponseBuilder;
use App\Core\Support\ThrowApi;

if (!function_exists('throwApi')) {
    function throwApi(): ThrowApi
    {
        return app(ThrowApi::class);
    }
}

if (!function_exists('responseBuilder')) {
    function responseBuilder(): ResponseBuilder
    {
        return app(ResponseBuilder::class);
    }
}
