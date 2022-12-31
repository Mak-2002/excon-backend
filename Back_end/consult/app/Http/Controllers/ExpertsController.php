<?php

namespace App\Http\Controllers;

use App\Models\{Appointment, WorkDay, Expert, Rating, User};
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
    public static function find_expert_by_user_id_or_fail($user_id, bool $throws_exception = true)
    {
        // abandoned the use of scope user  for efficiency issues
        $expert = Expert::whereHas(
            'user',
            fn($query) => $query->where('id', $user_id)
        )->first();
        if (is_null($expert)) {
            if ($throws_exception)
                throw new ItemNotFoundException(" EXPERT NOT FOUND ", 1);
            return null;
        }
        return $expert;
    }

    protected static function save_expert_and_return(Expert $expert)
    {
        if (!$expert->save())
            return response()->json([
                'success' => false,
                'message' => "could not save expert"
            ]);
        return response()->json([
            'success' => true,
            'message' => 'expert saved successfully'
        ]);
    }

    //TODO: save expert after editing

    public function update(Request $request)
    {
        $atts = array_keys($request->toArray());
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);
        foreach ($atts as $att) {
            if ($att == 'expert_id')
                continue;
            $expert->$att = $request->$att;
        }
        self::save_expert_and_return($expert);
    }
    public function upload_profile_photo(Request $request)
    {
        $expert = self::find_expert_by_user_id_or_fail($request->expert_id);
        // Store profile photo
        $image = $request->file('profile_photo');
        $path = $image->storeAs('public/profile_photos', $request->expert_id . '.jpg');
        // dd($path); //DEBUG
        $expert->photo_path = $path;
        self::save_expert_and_return($expert);
    }

    public function update_rating(Request $request)
    {
        // get expert by user id
        $expert = self::find_expert_by_user_id_or_fail($request->expert_id)->expert;
        $user = UsersController::find_user_or_fail($request->user_id);
        //dd($expert); //DEBUG
        $rating = Rating::where('user_id', $user->id)->andWhere('expert_id', $expert->id)->first();
        if(is_null($rating)) {
            $rating = new Rating;
            $rating->user_id = $user->id;
            $rating->expert_id = $expert->id;
            $expert->rating_count += 1;
            $rating->value = 0;
        }
        $expert->rating_sum += $request->rating - $rating->value;
        $rating->value = $request->rating;
        if (!$rating->save() || !$expert->save())
            return response()->json([
                'success' => false,
                'message' => 'could not update rating'
            ]);

        return response()->json([
            'success' => true,
            'message' => 'rating updated successfully'
        ]);
    }

    public function index(Request $request)
    {
        $query = Expert::latest()->with(['user', 'consultations'])
            ->filter([
                'consulttype' => $request->consulttype,
                'search' => $request->search
            ])->get();

        foreach ($query as $expert)
            if (!is_null($expert->consultations))
                $expert->consultations = $expert->consultations->toArray();

        return ($query->toJSON());
    }

    public function show(Request $request)
    {
        return response()->json([
            'expert' => ExpertsController::find_expert_by_user_id_or_fail($request->expert_id),
        ]);
    }

    public function schedule(Request $request)
    {
        return response()->json([
            'schedule' => ExpertsController::find_expert_by_user_id_or_fail($request->expert_id)->appointments,
        ]);

    }

    public function chats(Request $request)
    {
        return response()->json([
            'chats' => self::find_expert_by_user_id_or_fail($request->user_id)->chats,
        ]);
    }

    public function book(Request $request)
    {


        // time format in 24h
        $appointment = new Appointment;
        $user = UsersController::find_user_or_fail($request->user_id);
        $expert = self::find_expert_by_user_id_or_fail($request->expert_id);


        $appointment->date = $request->date;
        $appointment->start_time = $request->start_time;
        $appointment->end_time = $request->end_time;
        $appointment->setRelation('user', $user);
        $appointment->user_id = $user->id;
        $appointment->setRelation('expert', $expert);
        $appointment->expert_id = $expert->id;

        if (!$appointment->save())
            return response()->json([
                'success' => false,
                'message' => "could not save appointment"
            ]);
        return response()->json([
            'success' => true,
            'message' => 'appointment saved successfully'
        ]);
    }

    public function create_schedule(Request $request)
    {
        // time format in 24h
        $expert = self::find_expert_by_user_id_or_fail($request->expert_id);
        // dd($expert); //DEBUG
        $table = WorkDay::where('expert_id', $expert->id);
        foreach ($request->days as $work_day) {
            $day = mb_strtolower($work_day['day']);
            $row = $table->where('day', $day);
            //dd($row); //DEBUG
            if (!is_null($row))
                $row->delete(); // Delete in order to update
            $row = new Workday;
            //dd($work_day); //DEBUG
            $row->day = $day;
            $row->setRelation('expert', $expert);
            $row->expert_id = $expert->id;
            $row->is_available = $work_day['is_available'];
            if ($row->is_available) {
                $row->start_time_1 = $work_day['start_time_1'];
                $row->end_time_1 = $work_day['end_time_1'];
                if (array_key_exists('start_time_2', $work_day)) {
                    $row->start_time_2 = $work_day['start_time_2'];
                    $row->end_time_2 = $work_day['end_time_2'];
                }
            }
            if (!$row->save())
                return response()->json([
                    'success' => false,
                    'message' => 'could not updated schedule'
                ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'schedule updated successfully'
        ]);
    }
}