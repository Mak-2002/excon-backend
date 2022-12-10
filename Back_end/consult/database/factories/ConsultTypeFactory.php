<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{User, Consultation, ConsultType, Appointment, Chat, Expert, Favorite, Message, WorkDay};


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsultType>
 */
class ConsultTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type_en' => $this->faker->word,
            'type_ar' => 'استشارة'
        ];
    }
}
