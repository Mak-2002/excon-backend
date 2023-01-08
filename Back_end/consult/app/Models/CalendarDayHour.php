<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarDayHour extends Model
{
    use HasFactory;

    public function calendar_day()
    {
        return $this->belongsTo(CalendarDay::class);
    }

    public function occupied()
    {
        return $this->state == 1;
    }

    public function is_available()
    {
        return $this->state == 0;
    }
}