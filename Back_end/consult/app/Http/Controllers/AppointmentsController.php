<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    
    public function schedule()
    {
        return Appointment::all();
    }
}
