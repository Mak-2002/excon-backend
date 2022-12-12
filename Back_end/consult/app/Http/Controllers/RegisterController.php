<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    
    public function create(request $request)
    {
        $fields = $request->validate([

            'full_name_en' => 'required|min:3|max:55',

            'email' => 'required|email',

            'password' => 'required|min:3|max:55',

        ]);
        $user = new User();
        $user->full_name_en = $fields['full_name_en'];
        $user->email = $fields['email'];
        $user->password = bcrypt($fields['password']);
        if($user->save()){
            auth()->login($user);
            return response()->json([
                'success'=>true,
                'message'=>'successfully registered'
            ]);
        }
        else{
            return response()->json([
                'success'=>false
            ]);
        }

    }
    
}


// $user = new User();
// $user->full_name_en = $request->post('full_name_en');
// $user->email = $request->post('email');
// $user->password = bcrypt($request->post('password'));
// if($user->save()){
//     auth()->login($user);
//     return response()->json([
//         'success'=>true
//     ]);
// }
// else{
//     return response()->json([
//         'success'=>false
//     ]);
// }




// public function create(Request $request)
    // {
        // $fields = $request->validate([

        //     'full_name_ar' => 'required|min:3|max:55',

        //     'full_name_en' => 'required|min:3|max:55',

        //     'email' => 'required|email',

        //     'password' => 'required|min:3|max:55',

        // ]);
    //     $user = User::create([
    //         'full_name_ar' => $fields['full_name_ar'],
    //         'full_name_en' => $fields['full_name_en'],
    //         'email' => $fields['email'],
    //         'password' => bcrypt($fields['password']),

    //     ]);
    //     Auth::login($user);
    //     return response()->json([
    //         'success' => true,
    //         'message'=> 'user created successfully'
    //     ]);

    // }