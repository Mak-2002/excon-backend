<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatsController extends Controller
{
    public function get_chat(Request $request) {
        $user_1_id = min($request->user_1_id, $request->user_2_id);
        $user_2_id = max($request->user_1_id, $request->user_2_id);
        return Chat::where('user_1_id', $user_1_id)->andWhere('user_2_id', $user_2_id)->first()->messages->toJSON();
    }
}
