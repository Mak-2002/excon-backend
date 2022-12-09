<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultType extends Model
{
    use HasFactory;

    // Mass Assignment is turned OFF
    protected $gaurded = [];

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

}