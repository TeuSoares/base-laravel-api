<?php

namespace App\Core\Enums;

enum UserLanguage: string
{
    case PT_BR = 'pt_BR';
    case EN = 'en';
    case ES = 'es';

    public function label(): string
    {
        return match ($this) {
            self::PT_BR => 'Português (Brasil)',
            self::EN    => 'English',
            self::ES    => 'Español',
        };
    }
}
