<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatsController extends Controller
{
    public function get_chat(Request $request)
    {
        $user_1_id = min($request->user_id, $request->expert_id);
        $user_2_id = max($request->user_id, $request->expert_id);
        $chat = Chat::where('user_1_id', $user_1_id)->where('user_2_id', $user_2_id)->first();
        if (is_null($chat))
            $res = [
                'success' => false,
                'message' => 'chat not initiated yet between these two users'
            ];
        else
            $res = [
                'success' => true,
                'chat' => $chat->messages
            ];
        return response()->json([$res]);
    }
}