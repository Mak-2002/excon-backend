<?php

namespace App\Http\Controllers;

use App\Models\{Appointment, WorkDay, Expert, User};
use Database\Factories\WorkdayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ItemNotFoundException;
use PhpParser\JsonDecoder;

// $sql= new mysqli_connect();

class ExpertsController extends Controller
{
    /**
     * returns expert by its user id
     * @param mixed $user_id
     * @throws ItemNotFoundException 
     * @return Expert
     */
    public static function get_expert_by_user_id_or_fail($user_id) {
        // abandoned the use of scope user  for efficiency issues
        $expert = Expert::whereHas(
            'user',
            fn($query) => $query->where('id', $user_id)
        )->first();
        if(is_null($expert)) throw new ItemNotFoundException("EXPERT NOT FOUND", 1);
        return $expert;
    }

    public function update_rating(Request $request)
    {
        // get expert by user id
        $expert = self::get_expert_by_user_id_or_fail($request->user_id);
        //dd($expert); //DEBUG

        // update expert's rating
        $expert->update([
            'rating_sum' => $expert->rating_sum + $request->rating,
            'rating_count' => $expert->rating_count + 1
        ]);

        return response()->noContent();
    }

    public function index(Request $request)
    {
        $query = Expert::all()->with(['user', 'consultations'])
            ->filter([$request->consulttype, $request->search])->get();

        foreach ($query as $element)
            if ($element->consultations ?? false)
                $element->consultations = $element->consultations->toArray();

        return ($query->toArray());

    }

    public function show(Expert $expert)
    {
        return response()->json([

            'expert' => $expert,
        ]);
    }

    public function schedule(Expert $expert)
    {

        return response()->json([

            'schedule' => $expert->appointments,
        ]);

    }

    public function chats(Request $request)
    {
        $expert = self::get_expert_by_user_id_or_fail($request->user_id);
        return response()->json([

            'chats' => $expert->chats,
        ]);
    }

    public function book(Request $request)
    {
        $appointment = new Appointment;
        $user = UsersController::get_user_or_fail($request->user_id);
        $expert = self::get_expert_by_user_id_or_fail($request->expert_id);

        $appointment->date = $request->date;
        $appointment->start_time = $request->start_time;
        $appointment->end_time = $request->end_time;
        $appointment->setRelation('user', $user); 
        $appointment->setRelation('expert', $expert); 

        $appointment->save();
        return $appointment;

    }

    public function create_schedule(Request $request)
    {
        $expert = UsersController
        // dd($expert); //DEBUG
        $table = $expert->work_days;
        foreach ($request->days as $item) {
            $day = mb_strtolower($item['day']);
            $row = $table->where('day', $day);
            if ($row ?? false)
                $row->delete(); // Delete in order to update
            $row = new Workday;
            $row->day = $day;
            $row->setRelation('expert', $expert);
            $row->start_time_1 = $item->start_time_1;
            $row->end_time_1 = $item->end_time_1;
            $row->start_time_2 = $item->start_time_2;
            $row->end_time_2 = $item->end_time_2;
            $row->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'schedule updated successfully'
        ]);
    }
}