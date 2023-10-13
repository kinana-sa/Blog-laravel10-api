<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
            'title' => fake()->sentence($nbWords = 10, $variableNbWords = true,$maxNbChars = 50),
            'body' => fake()->paragraph(),
            'user_id' => User::factory()->create()->id
        ];
    }
}
