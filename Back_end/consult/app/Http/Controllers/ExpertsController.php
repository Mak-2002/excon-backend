<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpParser\Node\Expr;
use App\Models\{User, Expert, Consultation, ConsultType, Appointment, Favorite, Message, WorkDay, Chat};

class ExpertsController extends Controller
{

    public function index()
    {
        return Expert::latest()->with(['user','consultations'])->filter(\request(['consulttype' , 'search']))->get()->toJSON();
    }


    public function show(Expert $expert)
    {
        return response()->json([

            'expert' => $expert
        ]);
    }

    public function schedule(Request $request)
    {


        $expert = Expert::where('phone', $request['phone']);
        return response()->json([

            'schedule' => $expert->appointments->date
        ]);
    }
}
