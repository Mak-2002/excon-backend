<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarDay extends Model
{
    use HasFactory;

    public function hours() {
        return $this->hasMany(CalendarDayHour::class);
    }

    public function is_available() {
        return $this->available_hours_count > 0;
    }
}
