<?php

namespace App\Http\Controllers;

use App\Models\{User, Expert};
use Doctrine\Inflector\Rules\Turkish\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Guid\Fields;

class sessionsController extends Controller
{
    public function create(Request $request)
    {
        $is_expert = $request->bio_en ?? false;
        $request->validate([
            'name_en' => ['required', 'string', 'max:21'],
            'name_ar' => ['required', 'string', 'max:21'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required'],
        ]);

        $user = new User;
        $user->name_en = $request->name_en;
        $user->name_ar = $request->name_ar;
        $user->email = mb_strtolower($request->email);
        $user->password = bcrypt($request->password);

        if (!$user->save())
            return response()->json([
                'success' => false
            ]);

        $expert = new Expert;
        if ($is_expert) {
            $request->validate([
                'address_en' => 'min:5',
                'address_ar' => 'min:5',
                'bio_en' => 'required|min:5',
                'bio_ar' => 'min:5',
                'service_cost' => 'required|numeric',
            ]);
            $expert->address_en = $request->address_en;
            $expert->address_ar = $request->address_ar;
            $expert->bio_en = $request->bio_en;
            $expert->bio_ar = $request->bio_ar;
            $expert->service_cost = $request->service_cost;
            $expert->setRelation('user', $user);
            $expert->user_id = $user->id;
            if (!$expert->save())
            return response()->json([
                'success' => false
            ]);
        }



        auth()->login($user);
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'expert' => $expert->makeHidden('user')
        ]);
    }


    public function login(Request $request)
    {
        $attributes = $request->validate([

            'email' => 'required|email',
            'password' => 'required|min:3|max:255',
        ]);
        $attributes['email'] = mb_strtolower($attributes['email']);
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
            'expert' => ExpertsController::get_expert_by_user_id_or_fail($user->id, false)
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
