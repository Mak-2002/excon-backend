<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Consultation;
use App\Models\Appointment;
use App\Models\Chat;
use App\Models\Expert;
use App\Models\Favorite;
use App\Models\Message;
use App\Models\WorkDay;

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
