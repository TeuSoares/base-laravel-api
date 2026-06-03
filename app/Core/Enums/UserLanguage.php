<?php

namespace App\Core\Enums;

enum UserLanguage: string
{
    case PT_BR = 'pt_BR';
    case EN = 'en';
    case ES = 'es';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function toGatewayLocale(): string
    {
        return match ($this) {
            self::EN    => 'en',
            self::PT_BR => 'pt',
            self::ES    => 'es',
        };
    }
}
