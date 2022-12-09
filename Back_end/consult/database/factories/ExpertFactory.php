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
            'photo_id' =>$this->faker->numberBetween,
            'phone' =>$this->faker->phoneNumber,
            'address'=>$this->faker->text,
            'rating'=>3.0,
            'bio'=>$this->faker->paragraph,
            'service_cost'=>20.0,
            'user_id'=>User::factory(),
        ];
    }
}
