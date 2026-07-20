<?php

namespace Database\Factories;

use App\Models\SocialConnection;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialConnection>
 */
class SocialConnectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'provider' => $this->faker->randomElement(['facebook', 'instagram', 'tiktok', 'x', 'reddit']),
            'provider_account_id' => $this->faker->uuid(),
            'provider_account_name' => $this->faker->userName(),
            'access_token' => $this->faker->sha256(),
            'refresh_token' => $this->faker->sha256(),
            'token_expires_at' => now()->addDays(60),
            'refresh_token_expires_at' => now()->addDays(90),
        ];
    }
}
