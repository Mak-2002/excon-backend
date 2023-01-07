<?php

namespace App\Http\Controllers;

use App\Models\{ConsultType, User, Expert};
use App\Models\Consultation;
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'min:8'],
        ]);

        if (!is_null(UsersController::find_user_by_email_or_fail($request->email, false)))
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
                'success' => false,
                'message' => "could not create user"
            ]);

        $expert = new Expert;
        if ($user->is_expert) {
            $request->validate([
                'bio_en' => 'required',
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
                    'success' => false,
                    'message' => "created user but couldn't make them expert"
                ]);
            $count = 1;
            foreach ($request->consultations as $exists) {
                if(!$exists) 
                {
                    $count++;
                    continue;
                }
                $type = ConsultType::find($count);
                if (is_null($type))
                    return response()->json([
                        'success' => false,
                        'message' => 'could not find' + $count + 'th consultation item in database' //TODO
                    ]);
                $consult = new Consultation;
                $consult->setRelation('expert', $expert);
                $consult->expert_id = $expert->id;
                $consult->type_en = $type->type_en;
                $consult->type_ar = $type->type_ar;
                if (!$consult->save())
                    return response()->json([
                        'success' => false,
                        'message' => 'could not add' + $count + 'th  consultation item'
                    ]);
                $count++;
            }
        }

        auth()->login($user);

        return response()->json([
            'success' => true,
            'user' => $user,
            'expert' => $expert->makeHidden(['user', 'consultations']),
            'consultations' => $expert->consultations
        ]);
    }

    public function login(Request $request)
    {
        $attributes = $request->validate([

            'email' => 'required|email',
            'password' => 'required|min:8|max:255',
        ]);
        $attributes['email'] = mb_strtolower($attributes['email']);
        if (!Auth::attempt($attributes)) {
            return response([
                'success' => false,
                'message' => 'unauthorizaed',
            ]);
        }
        $user = Auth::user();
        $result = [
            'success' => true,
            'message' => 'successfully logged in',
            'user' => $user,
        ];

        $expert = ExpertsController::find_expert_by_user_id_or_fail($user->id, false);
        if(!is_null($expert)) {
            $expert->makeHidden(['user', 'consultations']);
            $result['expert'] = $expert;
            $result['consultations'] = $expert->consultations;
        }
        return response()->json([$result]);
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
