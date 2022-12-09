<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Consultation;
use App\Models\Appointment;
use App\Models\Chat;
use App\Models\Expert;
use App\Models\Favorite;
use App\Models\Message;
use App\Models\WorkDay;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::truncate();
        // Expert::truncate();
        // Chat::truncate();
        // Favorite::truncate();
        // Appointment::truncate();
        // Message::truncate();
        // Consultation::truncate();
        // WorkDay::truncate();

        User::factory(10)->create();
        Expert::factory(10)->create();
        Chat::factory(10)->create();
        Message::factory(10)->create();
        Appointment::factory(10)->create();
        Consultation::factory(10)->create();
        WorkDay::factory(10)->create();
        Favorite::factory(10)->create();
    }
}
