<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\User;
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
}
 // $expert->user->balance +=$expert->service_cost;
        // $user->balance -=$expert->service_cost;
