<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class sessionsController extends Controller
{
    public function login(Request $request)
    {

        $attributes = request()->validate([
            
            'email' => 'required|email',
            'password' => 'required|min:3|max:255',
        ]);
        if (!auth()->attempt($attributes)) {
            return response([
                'status' => 'error',
                'message' => 'unauthorizaed',
            ]);
        }
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'message' => 'successfully logged in',
            'user' => $user,
        ]);
    }
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'successfully logged out',
        ]);
    }
}
// $user=User::where('email' , $attributes['email'])->first();
// if(!$user || !Hash::check($attributes['password'], $user->password)){
//     return response([
//         'messsage'=>'wrong informations'
//     ]);
// }

// return redirect('/')->with('success', 'welcome back');
