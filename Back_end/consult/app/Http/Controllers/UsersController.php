<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Expert, Consultation, ConsultType, Appointment, Favorite, Message, WorkDay, Chat};

class UsersController extends Controller
{
    public function favor()
    {
        $expert = Expert::all()->user(request('expert_id'))->get();
        $user = User::where('id', request('user_id'))->get();
        $expert->update('fav_count', $expert->fav_count + 1);

        // Create favorite instance
        $favorite = new Favorite;
        $favorite->expert_id = $expert->id;
        $user->favorites->save($favorite);
    }
}