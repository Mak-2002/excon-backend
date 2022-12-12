<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Mass Assigment is turned OFF
    protected $gaurded = [];

    // public function expert() {
    //     return $this->hasOne(Expert::class);
    // }

    public function appiontments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }


    public function scopeFilter($query, array $filters)
    {
        $query->when(($filters['search'] ?? false), fn($query, $search) =>
            $query
                ->where('full_name_en', 'like', '%' . $filters['search'] . '%')) ;
    }

}