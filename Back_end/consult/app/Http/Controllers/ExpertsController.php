<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Expert;use App\Models\Expert\appointments;
use App\Models\WorkDay;use Illuminate\Http\Request;

// $sql= new mysqli_connect();

class ExpertsController extends Controller
{

    public function index()
    {
        return Expert::latest()->with(['user', 'consultations'])->filter(\request(['consulttype', 'search']))->get()->toJSON();
    }

    public function show(Expert $expert)
    {
        return response()->json([

            'expert' => $expert,
        ]);
    }

    public function schedule(Expert $expert)
    {

        return response()->json([

            'schedule' => $expert->appointments,
        ]);

    }

    public function chats(Expert $expert)
    {

        return response()->json([

            'chats' => $expert->chats,
        ]);

    }

    public function booking()
    {

        $appointment = new Appointment;

        $appointment->date = request('date');
        $appointment->start_time = request('start_time');
        $appointment->end_time = request('end_time');
        $appointment->expert_id = request('expert_id');
        $appointment->customer_id = request('user_id');

        $appointment->save();
        return $appointment;

    }

}
