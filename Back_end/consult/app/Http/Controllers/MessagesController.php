<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function index()
    {
        return Message::latest()->with(['chat'])->filter(\request(['chat']))->get()->toJSON();
    }
}
 