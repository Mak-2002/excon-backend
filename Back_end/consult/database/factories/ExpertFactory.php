<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\{User, Consultation, ConsultType, Appointment, Chat, Expert, Favorite, Message, WorkDay};


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
            'photo_id' => $this->faker->numberBetween,
            'phone' => $this->faker->phoneNumber,
            'address_en' => $this->faker->sentence,
            'address_ar' => $this->faker->sentence,
            'rating' => 3.0,
            'bio_en' => $this->faker->paragraph,
            'bio_ar' => $this->faker->paragraph,
            'service_cost' => 20.0,
            'user_id' => User::factory(),
        ];
    }
}