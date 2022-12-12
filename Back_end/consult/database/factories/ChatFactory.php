<?php

namespace Database\Factories;

use App\Models\{User, Consultation, ConsultType, Appointment, Chat, Expert, Favorite, Message, WorkDay};


use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'customer_id' => User::factory(),
            'expert_id' => Expert::factory(),
        ];
    }
}