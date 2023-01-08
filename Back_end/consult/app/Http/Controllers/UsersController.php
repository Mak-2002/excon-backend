<?php

namespace App\Http\Controllers;

use App\Models\{User, Consultation, Appointment, Chat, Expert, Favorite, Message, WorkDay};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;

class UsersController extends Controller
{
    /**
     * tries to find user by its email , fuck me
     * @param mixed $email
     * @param bool $throws_exception
     * @throws ItemNotFoundException 
     * @return mixed user
     */
    public static function find_user_by_email_or_fail($email, bool $throws_exception = true)
    {
        $user = User::where('email', $email)->first();
        if (is_null($user)) {
            if ($throws_exception)
                throw new ItemNotFoundException(" USER NOT FOUND ", 1);
            return null;
        }
        return $user;
    }

    /**
     * find user by it's id or fail
     * @param mixed $user_id
     * @throws ItemNotFoundException
     * @return mixed User
     */
    public static function find_user_or_fail($user_id, bool $throws_exception = true)
    {
        $user = User::find($user_id);
        if (is_null($user)) {
            if ($throws_exception)
                throw new ItemNotFoundException(" USER NOT FOUND ", 1);
            return null;
        }
        return $user;
    }

    public static function temp_login_for_postman()
    { // DEBUG
        if (App::hasDebugModeEnabled())
            Auth::attempt([
                'email' => 'love@gmail.com',
                'password' => 'helloguyswelcometomyyoutubechannel'
            ]); // (Because Postman Doesn't save session data in)
    }

    public function send_message(Request $request)
    {

        $user_1_id = min($request->sender_id, $request->receiver_id);
        $user_2_id = max($request->sender_id, $request->receiver_id);
        $chat = Chat
            ::where('user_1_id', $user_1_id)
            ->where('user_2_id', $user_2_id)->first();
        if (is_null($chat)) {
            $chat = new Chat;
            $chat->user_1_id = $user_1_id;
            $chat->user_2_id = $user_2_id;
            $chat->setRelation('user_1', self::find_user_or_fail($user_1_id));
            $chat->setRelation('user_2', self::find_user_or_fail($user_2_id));
            if (!$chat->save())
                return response()->json([
                    'success' => false,
                    'message' => 'could not initiate chat'
                ]);
        }
        $message = new Message;
        $message->content = $request->content;
        $message->sender_id = $request->sender_id;
        $message->receiver_id = $request->receiver_id;
        $message->setRelation('chat', $chat);
        $message->chat_id = $chat->id;
        if (!$message->save())
            return response()->json([
                'success' => false,
                'message' => 'could not send message'
            ]);
        return response()->json([
            'success' => true,
            'message' => 'message sent successfully'
        ]);
    }

    public function chats(Request $request)
    {
        $chats = Chat::where('user_1_id', $request->user_id)->orWhere('user_2_id', $request->user_id)->with('messages')->get();
        foreach ($chats as $chat) {
            $other_user_id = $chat->user_1_id;
            if($other_user_id == $request->user_id) $other_user_id = $chat->user_2_id;
            $chat['other_user_name'] = self::find_user_or_fail($other_user_id)->name;
        }
        return response()->json($chats);
    }
    public function pay(Request $request)
    {
        $user = UsersController::find_user_or_fail($request->user_id);
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);

        if ($user->balance < $expert->service_cost)
            return response()->json([
                'success' => false,
                'message' => 'not enough balance'
            ]);
        $expert_user = $expert->user;
        $user->balance -= $expert->service_cost;
        $expert_user->balance += $expert->service_cost;

        if (!$user->save() || !$expert_user->save())
            $res = [
                'success' => false,
                'message' => 'could not make payment'
            ];
        else
            $res = [
                'success' => true,
                'message' => 'payment approved'
            ];
        // dd(response()->json([
        //     'expert' => $expert,
        //     'user' => $user
        // ])); //DEBUG  
        return response()->json([$res]);
    }

    public function change_favorite_state(Request $request)
    {
        $user = self::find_user_or_fail($request->user_id);
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);
        // dd($expert); //DEBUG
        $favorite = Favorite::where('user_id', $user->id)->where('expert_id', $expert->id);
        if ($favorite->exists()) {
            $expert->fav_count -= 1;
            if (!$expert->save() || !$favorite->delete())
                return response()->json([
                    'success' => false,
                    'message' => 'could not remove from favorites'
                ]);
            return response()->json([
                'success' => true,
                'message' => 'removed from favorites successfully'
            ]);
        }
        $expert->fav_count += 1;
        // Create favorite instance
        $favorite = new Favorite;
        $favorite->setRelation('user', $user);
        $favorite->user_id = $user->id;
        $favorite->setRelation('expert', $expert);
        $favorite->expert_id = $expert->id;
        if (!$favorite->save() or !$expert->save())
            return response()->json([
                'success' => false,
                'message' => 'could not add to favorites'
            ]);

        // return $favorite->toJSON(); //DEBUG
        return response()->json([
            'success' => true,
            'message' => 'added to favorites successfully'
        ]);
    }

    public function favorites(Request $request)
    {

        $favs = Expert::with('user')->whereHas(
            'favorable_by',
            fn($query) =>
            $query->where('user_id', $request->user_id)
        )->get();
        return response()->json($favs);
    }
}