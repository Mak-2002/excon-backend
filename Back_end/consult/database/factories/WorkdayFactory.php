<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\{User, Consultation, ConsultType, Appointment, Chat, Expert, Favorite, Message, WorkDay};


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