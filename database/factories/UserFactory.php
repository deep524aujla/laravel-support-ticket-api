<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
            'remember_token' => Str::random(10),
            'role_id' => Role::where('slug', 'customer')->first()?->id,
            'is_active' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('slug', 'admin')->first()?->id,
        ]);
    }

    public function agent(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('slug', 'agent')->first()?->id,
        ]);
    }

    public function customer(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('slug', 'customer')->first()?->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
