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

    public function period_1_is_available() {
        return $this->first_av_st_1 < $this->end_time_1;
    }

    public function period_2_is_available() {
        return $this->first_av_st_2 < $this->end_time_2;
    }

    public function is_available() {
        return $this->period_1_is_available() || $this->period_2_is_available();
    }
}
