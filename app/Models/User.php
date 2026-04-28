<?php

namespace App\Models;

use App\Core\Enums\CountryCode;
use App\Core\Enums\UserLanguage;
use App\Modules\Auth\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'language' => UserLanguage::class,
            'country_code' => CountryCode::class,
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $baseUrl = config('app.frontend_url', env('FRONT_URL'));

        $url = "{$baseUrl}/password/reset/{$token}?email=" . urlencode($this->email);

        $this->notify(new ResetPasswordNotification($url, $this->name));
    }

    public function subscribed()
    {
        //
    }
}
