<?php

namespace Database\Factories;

use App\Models\{User, Consultation, Appointment, Chat, Expert, Favorite, Message, WorkDay};

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'start_date' => $this->faker->dateTime,
            'end_date' => $this->faker->dateTime,
            'user_id' => User::factory(),
            'expert_id' => Expert::factory()
        ];
    }
}
