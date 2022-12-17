<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Expert;use App\Models\Expert\appointments;
use App\Models\WorkDay;use Illuminate\Http\Request;

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
        // $to_be_sent_array = array();

        // $counter = 0; foreach ($query as $elemment) {
        //     $to_be_sent_array += [
        //         [
        //             'user_id' => $elemment->user_id,
        //             'full_name_en' => $elemment->user->full_name_en,
        //             'full_name_ar' => $elemment->user->full_name_ar,
        //             'email' => $elemment->user->email,
        //             'phone' => $elemment->user->phone,
        //             'photo_id' => $elemment->photo_id,
        //             'address_en' => $elemment->address_en,
        //             'address_ar' => $elemment->address_ar,
        //             'balance' => $elemment->user->balance,
        //             'rating' => $elemment->rating,
        //             'bio_en' => $elemment->bio_en,
        //             'bio_ar' => $elemment->bio_ar,
        //             'service_cost' => $elemment->service_cost,
        //             'consultations' => array_map(
        //                 function ($consult) {
        //                     $consults = array();
        //                     $consults += [
        //                         [
        //                             'consult_id' => $consult->id,
        //                             'type_en' => $consult->type_en,
        //                             'type_ar' => $consult->type_ar
        //                         ]
        //                     ];
        //                     return $consults;
        //                 }
        //                 , $element->consultations
        //             )
        //         ]
        //     ];
        // }
        // dd($to_be_sent_array);
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