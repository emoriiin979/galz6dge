<?php

namespace Database\Factories;

use App\Libs\HatenaXml;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $xml = new HatenaXml();
        $now = Carbon::now();

        return [
            'id' => $this->faker->unique()->randomNumber(9),
            'title' => $this->faker->name,
            'edited_at' => $now,
            'is_modified' => false,
            'body' => $xml->getEntryXml(),
        ];
    }
}
