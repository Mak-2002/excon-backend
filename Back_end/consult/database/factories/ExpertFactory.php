<?php

namespace Database\Factories;
use App\Models\{User, Consultation, Appointment, Chat, Expert, Favorite, Message, WorkDay};

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expert>
 */
class ExpertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'photo_path' => $this->faker->numberBetween,
            'address_en' => $this->faker->sentence,
            'address_ar' => $this->faker->sentence,
            'rating_sum' => 11,
            'rating_count' => 4,
            'bio_en' => $this->faker->paragraph,
            'bio_ar' => $this->faker->paragraph,
            'service_cost' => 20.0,
            'user_id' => User::factory(),
        ];
    }
}
