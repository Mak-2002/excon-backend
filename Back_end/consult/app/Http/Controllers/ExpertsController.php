<?php

namespace App\Http\Controllers;

use App\Models\{Appointment, WorkDay, Expert, Favorite, Rating, User};
use Database\Factories\WorkdayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
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

    public function get_schedule(Expert $expert)
    {
        $table = WorkDay::where('expert_id', $expert->id);
        if (!$table->exists())
            return null;
        $table = $table->get();
        $res = [];
        $res['start_time'] = $table->first()->start_time_1;
        $res['end_time'] = $table->first()->end_time_1;
        $days = [];
        foreach ($table as $element)
            $days[$element->day] = $element->is_available;
        $res['days'] = $days;
        return $res;
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

    public function update(Request $request)
    {
        $atts = array_keys($request->toArray());
        $expert = self::find_expert_by_user_id_or_fail($request->expert_id);
        if (!$expert->exists())
            throw new ItemNotFoundException(" EXPERT NOT FOUND ", 1);
        foreach ($atts as $att) {
            if ($att == 'expert_id')
                continue;
            $expert->$att = $request->$att;
        }
        if (!$expert->save())
            return response()->json([
                'success' => false,
                'messsage' => 'could not update expert'
            ]);
        return response()->json([
            'success' => true,
            'message' => 'expert updated successfully',
            'expert' => $expert
        ]);
    }

    public function in_favorites(Request $request)
    {
        $user = UsersController::find_user_or_fail($request->user_id);
        $expert = self::find_expert_by_user_id_or_fail($request->expert_id);
        $fav = Favorite::where('user_id', $request->user_id)->where('expert_id', $expert->id);
        return $fav->exists();
    }

    public function upload_profile_photo(Request $request)
    {
        $expert_id = (int) $request->expert_id;
        // dd(gettype($request->expert_id));
        $expert = self::find_expert_by_user_id_or_fail($expert_id);
        // Store profile photo
        $image = $request->file('profile_photo');
        $path = $image->storeAs('public/profile_photos', $expert_id . '.jpg');
        dd(asset($path));
        // dd($path); //DEBUG
        $expert->photo_path = $path;
        if (!$expert->save())
            $res = [
                'success' => false,
                'message' => 'could not save expert after updating their profile photo'
            ];
        else
            $res = [
                'success' => true,
                'message' => 'profile photo updated successfully'
            ];
        return response()->json([$res]);
    }

    public function update_rating(Request $request)
    {
        $user = UsersController::find_user_or_fail($request->user_id);
        $expert = self::find_expert_by_user_id_or_fail($request->expert_id);
        // dd($expert->toArray()); //DEBUG

        $rating = Rating::where('user_id', $user->id)->where('expert_id', $expert->id)->first();
        if (is_null($rating)) {
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
        // dd($rating->toArray()); // DEBUG
        return response()->json([
            'success' => true,
            'message' => 'rating updated successfully'
        ]);
    }

    public function index(Request $request)
    {
        $curr_expert = self::find_expert_by_user_id_or_fail($request->expert_id, false);
        $to_exclude_expert_id = -1;
        if (!is_null($curr_expert))
            $to_exclude_expert_id = $curr_expert->id;
        $query = Expert::latest()->with(['user', 'consultations'])
            ->filter([
                'consulttype' => $request->consulttype,
                'search' => $request->search
            ])
            ->where('id', '!=', $to_exclude_expert_id)
            ->get();

        foreach ($query as $expert)
            if (!is_null($expert->consultations))
                $expert->consultations = $expert->consultations->toArray();

        return ($query->toJSON());
    }

    public function show(Request $request)
    {
        $expert = self::find_expert_by_user_id_or_fail($request->expert_id);
        $current_user = UsersController::find_user_or_fail($request->expert_id);
        $res = [
            'success' => true,
            'message' => 'expert found successfully',
            'expert' => $expert,
            'in_favorites' => Favorite
                ::where('user_id', $current_user->id)
                ->where('expert_id', $expert->id)
                ->exists(),
        ];
        if (!is_null(self::get_schedule($expert)))
            $res += self::get_schedule($expert);
        return response()->json([$res]);
    }

    public function appointments(Request $request)
    {
        return response()->json(
            self::find_expert_by_user_id_or_fail($request->expert_id)->expert->appointments->makeHidden('expert_id')
        );
    }

    public function chats(Request $request)
    {
        return response()->json([
            'chats' => self::find_expert_by_user_id_or_fail($request->expert_id)->chats,
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

    public function update_schedule(Request $request)
    {
        // time format in 24h


        $expert = self::find_expert_by_user_id_or_fail($request->expert_id);
        // dd($expert); //DEBUG
        $table = WorkDay::where('expert_id', $expert->id);
        foreach ($request->days as $dayName => $is_available) {
            $row = new Workday;
            //dd($work_day); //DEBUG
            $row->day = $dayName;
            $row->setRelation('expert', $expert);
            $row->expert_id = $expert->id;
            $row->is_available = $is_available;
            if ($row->is_available) {
                $row->start_time_1 = $request->start_time_1;
                $row->end_time_1 = $request->end_time_1;
                if ($request->start_time_2 ?? false) {
                    $row->start_time_2 = $request->start_time_2;
                    $row->end_time_2 = $request->end_time_2;
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