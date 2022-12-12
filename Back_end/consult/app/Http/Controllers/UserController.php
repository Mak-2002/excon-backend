<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, ConsultType};

class UserController extends Controller
{
    protected function index()
    {
        return view('welcome', [
            'users' => User::latest()->filter(request(['search']))->get()
        ]);
    }

    protected function showConsultTypes()
    {
        return view('consultations', [
            'types' => ConsultType::with('consultations')->get()
        ]);
    }
}