<?php

namespace App\Http\Controllers;

use App\Models\{User, Consultation, Appointment, Chat, Expert, Favorite, Message, WorkDay};
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function chats(User $user)
    {
       
        return response()->json([

            'schedule' => $user->chats
        ]);
     
        
    }

    public function pay (User $user , Expert $expert)
    {
        
        $user2 = $expert->user;
       $user->update([
        'balance' => $user->balance -=$expert->service_cost,
       ]);
       $user2->update([
        'balance' => $user2->balance +=$expert->service_cost,
       ]);
       
       return response()->json([

        'expert' => $expert,
        'user'=>$user
    ]);
        
    }

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
