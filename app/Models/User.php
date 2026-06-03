<?php

namespace App\Models;

use App\Core\Enums\CountryCode;
use App\Core\Enums\UserLanguage;
use App\Modules\Auth\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
    'country_code',
    'email_verified_at',
    'language',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasLocalePreference
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'language' => UserLanguage::class,
            'country_code' => CountryCode::class,
        ];
    }

    public function preferredLocale(): string
    {
        return $this->language?->value ?? config('app.locale');
    }

    public function sendPasswordResetNotification($token): void
    {
        $baseUrl = config('app.frontend_url', env('FRONT_URL'));

        $url = "{$baseUrl}/reset-password/{$token}?email=" . urlencode($this->email);

        $this->notify(new ResetPasswordNotification($url, $this->name));
    }
}
