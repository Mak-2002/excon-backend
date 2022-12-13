<?php

namespace Database\Factories;
use App\Models\{User, Consultation, Appointment, Chat, Expert, Favorite, Message, WorkDay};

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
           'sender_id' =>$this->faker->numberBetween(0, 10),
            'receiver_id' =>$this->faker->numberBetween(0, 10),
            'chat_id'=>Chat::factory(),
            'content' => $this->faker->sentence,
        ];
    }
}
