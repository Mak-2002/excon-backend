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
     * @return mixed Expert
     */
    public static function get_expert_by_user_id_or_fail($user_id, bool $throws_exception = true)
    {
        // abandoned the use of scope user  for efficiency issues
        $expert = Expert::whereHas(
            'user',
            fn($query) => $query->where('id', $user_id)
        )->first();
        if (is_null($expert)) {
            if ($throws_exception)
                throw new ItemNotFoundException("EXPERT NOT FOUND", 1);
            return null;
        }
        return $expert;
    }

    public function upload_profile_photo(Request $request)
    {
        $expert = self::get_expert_by_user_id_or_fail($request->expert_id);
        // Store profile photo
        $image = $request->file('profile_photo');
        if (!is_null($image)) {
            $path = $image->storeAs('app/public/profile_photos', $request->expert_id);
            $expert->photo_path = $path;
        }
        return response()->json([
            'success' => true,
            'message' => 'profile photo uploaded successfully'
        ]);
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

        //return $expert->toJSON(); //DEBUG
        return response()->noContent();
    }

    public function index(Request $request)
    {
        $query = Expert::all()->with(['user', 'consultations'])
            ->filter([
                'consulttype' => $request->consulttype,
                'search' => $request->search
            ])->get();

        foreach ($query as $element)
            if (!is_null($element->consultations))
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
        // time format in 24h
        $appointment = new Appointment;
        $user = UsersController::get_user_or_fail($request->user_id);
        $expert = self::get_expert_by_user_id_or_fail($request->expert_id);

        $appointment->date = $request->date;
        $appointment->start_time = $request->start_time;
        $appointment->end_time = $request->end_time;
        $appointment->setRelation('user', $user);
        $appointment->user_id = $user->id;
        $appointment->setRelation('expert', $expert);
        $appointment->expert_id = $expert->id;

        $appointment->save();
        return $appointment; //DEBUG
        //return response()->noContent();

    }

    public function create_schedule(Request $request)
    {
        // time format in 24h
        $expert = self::get_expert_by_user_id_or_fail($request->expert_id);
        // dd($expert); //DEBUG
        $table = WorkDay::where('expert_id', $expert->id);
        foreach ($request->days as $item) {
            $day = mb_strtolower($item['day']);
            $row = $table->where('day', $day);
            //dd($row); //DEBUG
            if (!is_null($row))
                $row->delete(); // Delete in order to update
            $row = new Workday;
            //dd($item); //DEBUG
            $row->day = $day;
            $row->setRelation('expert', $expert);
            $row->expert_id = $expert->id;
            $row->start_time_1 = $item['start_time_1'];
            $row->end_time_1 = $item['end_time_1'];
            if (array_key_exists('start_time_2', $item))
                $row->start_time_2 = $item['start_time_2'];
            if (array_key_exists('end_time_2', $item))
                $row->end_time_2 = $item['end_time_2'];
            $row->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'schedule updated successfully'
        ]);
    }
}