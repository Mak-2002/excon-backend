<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    public function get_chat(Request $request)
    {
        $user_1_id = min($request->user_id, $request->expert_id);
        $user_2_id = max($request->user_id, $request->expert_id);
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);
        $chat = Chat::where('user_1_id', $user_1_id)->where('user_2_id', $user_2_id)->first();
        if (is_null($chat))
            $res = [
                'success' => false,
                'message' => 'chat not yet initiated between these two users'
            ];
        else
            $res = [
                'success' => true,
                'other_user_name' => $expert->user->name,
                'chat' => $chat->messages
            ];
        return response()->json($res);
    }
}