<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatsController extends Controller
{
    public function get_chat(Request $request)
    {
        $user_1_id = min($request->curr_user_id, $request->other_user_id);
        $user_2_id = max($request->curr_user_id, $request->other_user_id);
        $other_user = UsersController::find_user_or_fail($request->other_user_id);
        $chat = Chat::where('user_1_id', $user_1_id)->where('user_2_id', $user_2_id)->first();
        if (is_null($chat))
            $res = [
                'success' => false,
                'message' => 'chat not yet initiated between these two users'
            ];
        else
            $res = [
                'success' => true,
                'other_user_name' => $other_user->name,
                'chat' => $chat->messages
            ];
        return response()->json([$res]);
    }
}