<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\{User, Consultation, ConsultType, Appointment, Chat, Expert, Favorite, Message, WorkDay};


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'customer_id'=>User::factory(),
            'expert_id'=>Expert::factory(),
        ];
    }
}
