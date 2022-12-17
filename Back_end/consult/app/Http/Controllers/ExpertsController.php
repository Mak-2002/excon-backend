<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Expert;use App\Models\Expert\appointments;
use App\Models\WorkDay;use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// $sql= new mysqli_connect();

class ExpertsController extends Controller
{

    public function update_rating()
    {
        // get expert by user id
        $query = Expert::latest()->user(request('expert_id'));
        $expert = $query->first();
        //dd($expert); // DEBUG


        // update expert's rating
        $query->update([
            'rating_sum' => $expert->rating_sum+ request('rating'),
            'rating_count' => $expert->rating_count + 1
        ]);

        return response(200);
    }

    public function index()
    {
        $query = Expert::latest()->with(['user', 'consultations'])
            ->filter(request(['consulttype', 'search']))->get();

        foreach ($query as $element)
            if ($element->consultations ?? false)
                $element->consultations = $element->consultations->toArray();

        return ($query->toArray());

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