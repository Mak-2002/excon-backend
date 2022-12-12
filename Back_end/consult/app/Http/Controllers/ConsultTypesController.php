<?php

namespace App\Http\Controllers;

use App\Models\ConsultType;
use Illuminate\Http\Request;

class ConsultTypesController extends Controller
{
    public function index()
    {
        return ConsultType::all();
    }
}
