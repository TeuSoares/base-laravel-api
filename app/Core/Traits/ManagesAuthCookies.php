<?php

namespace App\Core\Traits;

trait ManagesAuthCookies
{
    protected function setAuthCookie(bool $remember = false)
    {
        $duration = $remember ? config('session.remember_lifetime') : config('session.lifetime');
        return cookie('app_is_logged', 'true', $duration, '/', null, config('session.secure'), false);
    }

    protected function clearAuthCookie()
    {
        return cookie()->forget('app_is_logged');
    }
}
