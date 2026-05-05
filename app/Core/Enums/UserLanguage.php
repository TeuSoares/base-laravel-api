<?php

namespace App\Core\Enums;

enum UserLanguage: string
{
    case PT_BR = 'pt_BR';
    case EN = 'en';
    case ES = 'es';

    public function label(): string
    {
        return __("languages.{$this->value}");
    }

    public function flag(): string
    {
        return match ($this) {
            self::PT_BR => '🇧🇷',
            self::EN    => '🇺🇸',
            self::ES    => 'es',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => "{$case->flag()} {$case->label()}",
        ])->toArray();
    }
}
