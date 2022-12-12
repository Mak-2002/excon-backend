<?php

namespace Database\Factories;
use App\Models\{User, Consultation, ConsultType, Appointment, Chat, Expert, Favorite, Message, WorkDay};

use Illuminate\Database\Eloquent\Factories\Factory;

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
