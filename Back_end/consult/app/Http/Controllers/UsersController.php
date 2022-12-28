<?php

namespace App\Http\Controllers;

use App\Models\{User, Consultation, Appointment, Chat, Expert, Favorite, Message, WorkDay};
use Illuminate\Http\Request;
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
                throw new ItemNotFoundException("USER NOT FOUND", 1);
            return null;
        }
        return $user;
    }


    public function send_message(Request $request)
    {
        $sender_id = $request->sender_id;
        $receiver_id = $request->receiver_id;
        $user_1_id = min($sender_id, $receiver_id);
        $user_2_id = max($sender_id, $receiver_id);
        $chat = Chat
            ::where('user_1_id', $user_1_id)
            ->andWhere('user_2_id', $user_2_id)->first();
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
        $message->chat_id->$chat->id;
        if(!$message->save())
            return response()->json([
                'success'=>false,
                'message'=>'could not send message'
            ]);
        return response()->json([
            'success' => true,
            'message' => 'message sent successfully'
        ]);
    }

    public function chats(Request $request)
    {
        $chats = Chat::where('user_1_id', $request->user_id)->orWhere('user_2_id', $request->user_id);
        return $chats->toJSON();
    }
    public function pay(Request $request)
    {
        $expert = $this->find_user_or_fail($request->expert_id);
        $user = $this->find_user_or_fail($request->user_id);

        if ($user->balance < $expert->service_cost)
            return response()->json([
                'success' => false,
                'message' => 'not enough balance'
            ]);

        $user2 = $expert->user;
        $user->balance -= $expert->service_cost;
        $user2->balance += $expert->service_cost;

        return response()->json([
            'success' => true,
            'message' => 'payment approved'
        ]);

        // dump(response()->json([
        //     'expert' => $expert,
        //     'user' => $user
        // ])); //DEBUG    

    }

    public function add_favorite(Request $request)
    {
        if ($request->expert_id == $request->user_id)
            return response()->json([
                'success' => false,
                'message' => "can't add expert to it's favorites"
            ]);
        $expert = $this->find_user_or_fail($request->expert_id);
        $user = $this->find_user_or_fail($request->user_id);
        // dd($expert); //DEBUG

        $expert->fav_count += 1;
        // Create favorite instance
        $favorite = new Favorite;
        $favorite->setRelation('user', $user);
        $favorite->user_id = $user->id;
        $favorite->setRelation('expert', $expert);
        $favorite->expert_id = $expert->id;
        $favorite->save();

        // return $favorite->toJSON(); //DEBUG
        return response()->json([
            'success' => true,
            'message' => 'added to favorites successfully'
        ]);
    }

    public function favorites(Request $request)
    {
        $favs = Expert::whereHas(
            'favorableBy',
            fn($query) =>
            $query
                ->where('user_id', $request->user_id)
        )->get();
        return $favs->toJSON();
    }

}