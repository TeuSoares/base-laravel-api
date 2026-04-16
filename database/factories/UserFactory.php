<?php

namespace Database\Factories;

use App\Core\Enums\UserLanguage;
use App\Models\User;
use App\Modules\Auth\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'document_type' => fake()->randomElement(DocumentType::cases()),
            'document_number' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('55###########'),
            'country_code' => fake()->randomElement(['BR', 'US', 'GB']),
            'birth_date' => fake()->date('Y-m-d', '-18 years'),
            'language' => fake()->randomElement(UserLanguage::cases()),
            'remember_token' => Str::random(10),
        ];
    }

    public function international(): static
    {
        return $this->state(fn(array $attributes) => [
            'document_type' => 'PASSPORT',
            'country_code'  => 'US',
            'phone'         => fake()->numerify('1##########'),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
