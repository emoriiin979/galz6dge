<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Log>
 */
class LogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level = $this->faker->randomElement(['INFO', 'ERROR']);

        return [
            'level' => $level,
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'PATCH']),
            'url' => $this->faker->url,
            'key' => $this->faker->isbn10,
            'response_code' => $level ? 200 : 409,
            'message' => $this->faker->realText(32),
        ];
    }
}
