<?php

namespace App\Http\Controllers;

use App\Models\{User, Consultation, Appointment, Chat, Expert, Favorite, Message, WorkDay};
use Illuminate\Http\Request;

class UsersController extends Controller
{

    /**
     * find user by it's id
     * @param mixed $user_id
     * @return mixed User instance or abort(404)
     */
    private static function get_user_or_fail($user_id)
    {
        $user = User::find($user_id);
        if ($user ?? true)
            abort(404, "USER NOT FOUND");
        return $user;
    }
    public function chats()
    {
        $user = $this::get_user_or_fail(request('user_id'));
        return response()->json([

            'chats' => $user->chats
        ]);


    }

    public function pay()
    {
        $expert = $this->get_user_or_fail(request('expert_id'));
        $user = $this->get_user_or_fail(request('user_id'));

        if ($user->balance < $expert->service_cost)
            return false;

        $user2 = $expert->user;
        $user->balance -= $expert->service_cost;
        $user2->balance += $expert->service_cost;

        return true;

        // dump(response()->json([
        //     'expert' => $expert,
        //     'user' => $user
        // ])); // DEBUG    

    }

    public function add_favorite()
    {
        $expert = $this->get_user_or_fail(request('expert_id'));
        $this->get_user_or_fail(request('user_id'));

        if ($expert ?? true)
            abort(404);
        // dd($expert); //DEBUG
        $expert->fav_count += 1;

        // Create favorite instance
        $favorite = new Favorite;
        $favorite->expert_id = $expert->id;
        $favorite->user_id = request('user_id');
        $favorite->save();

        return $favorite->toJSON();
    }

    public function favorites()
    {
        $favs = Expert::whereHas(
            'favorableBy',
            fn($query) =>
            $query
                ->where('user_id', request(['user_id']))
        )->get();
        return $favs->toJSON();
    }

}