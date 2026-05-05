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
    case GERMANY = 'DE';
    case UNITED_KINGDOM = 'GB';

    public function label(): string
    {
        return __("countries.{$this->value}");
    }

    public function flag(): string
    {
        return match ($this) {
            self::BRAZIL => '🇧🇷',
            self::UNITED_STATES => '🇺🇸',
            self::ARGENTINA => '🇦🇷',
            self::PORTUGAL => '🇵🇹',
            self::CANADA => '🇨🇦',
            self::SPAIN => '🇪🇸',
            self::FRANCE => '🇫🇷',
            self::GERMANY => '🇩🇪',
            self::UNITED_KINGDOM => '🇬🇧',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => "{$case->flag()} {$case->label()}",
        ])->toArray();
    }

    public function isEuropeanUnion(): bool
    {
        return in_array($this, [
            self::PORTUGAL,
            self::SPAIN,
            self::FRANCE,
            self::GERMANY,
        ]);
    }
}
