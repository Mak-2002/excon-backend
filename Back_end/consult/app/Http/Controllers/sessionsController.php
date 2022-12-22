<?php

namespace App\Http\Controllers;

use App\Models\{User, Expert};
use Doctrine\Inflector\Rules\Turkish\Rules;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Guid\Fields;

class sessionsController extends Controller
{
    public function create(Request $request)
    {
        
        $request->validate([
            'name_en' => ['required', 'string', 'max:22'],
            'name_ar' => ['required', 'string', 'max:22'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required'],
        ]);

        if(is_null(UsersController::find_user_by_email_or_fail($request->email, false)))
            throw new Exception(" USER ALREADY EXISTS ", 1);
            

        $user = new User;
        $user->name_en = $request->name_en;
        $user->name_ar = $request->name_ar;
        $user->phone = $request->phone;
        $user->email = mb_strtolower($request->email);
        $user->password = bcrypt($request->password);
        $user->is_expert = ($request->service_cost > 0);

        if (!$user->save())
            return response()->json([
                'success' => false
            ]);

        $expert = new Expert;
        if ($user->is_expert) {
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
                'success' => false,
                'message' => 'unauthorizaed',
            ]);
        }
        $user = Auth::user();
        return response()->json([
            'success' => true,
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
