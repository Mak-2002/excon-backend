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
            'date' => $this->faker->date,
            'start_time' => $this->faker->time,
            'end_time' => $this->faker->time,
            'customer_id' => User::factory(),
            
        ];
    }
}
