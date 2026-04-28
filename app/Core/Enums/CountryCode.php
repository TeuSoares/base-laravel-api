<?php

namespace App\Core\Enums;

enum CountryCode: string
{
    case BRAZIL = 'BR';
    case UNITED_STATES = 'US';
    case ARGENTINA = 'AR';
    case PORTUGAL = 'PT';
    case CANADA = 'CA';
    case SPAIN = 'ES';
    case FRANCE = 'FR';

    public function label(): string
    {
        return match ($this) {
            self::BRAZIL => 'Brasil',
            self::UNITED_STATES => 'Estados Unidos',
            self::ARGENTINA => 'Argentina',
            self::PORTUGAL => 'Portugal',
            self::CANADA => 'Canadá',
            self::SPAIN => 'Espanha',
            self::FRANCE => 'França',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }
}
