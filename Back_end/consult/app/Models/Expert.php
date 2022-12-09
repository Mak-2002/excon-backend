<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expert extends Model
{
    use HasFactory;

    // Mass Assignment is turned OFF
    protected $gaurded = [];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultations() {
        return $this->hasMany(Consultation::class);
    }

    public function workdays() {
        return $this->hasMany(WorkDay::class);
    }
}