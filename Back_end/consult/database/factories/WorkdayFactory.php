<?php

namespace Database\Factories;
use App\Models\{User, Consultation, Appointment, Chat, Expert, Favorite, Message, WorkDay};

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workday>
 */
class WorkdayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'day' => $this->faker->dayOfWeek,
            'start_time_1' => $this->faker->time,
            'end_time_1' => $this->faker->time,
            'expert_id' => Expert::factory()
        ];
    }
}
