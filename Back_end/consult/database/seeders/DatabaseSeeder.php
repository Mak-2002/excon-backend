<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use App\Models\{User, Consultation, Appointment, Chat, ConsultType, Expert, Favorite, Message, WorkDay};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();
        Expert::factory(10)->create();
        Chat::factory(10)->create();
        Message::factory(10)->create();
        Appointment::factory(10)->create();
        Consultation::factory(10)->create();
        // WorkDay::factory(10)->create();
        Favorite::factory(10)->create();
    }
}
