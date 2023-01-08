<?php

namespace App\Http\Controllers;

use App\Models\{Appointment, CalendarDay, CalendarDayHour, Expert, WorkDay};
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\CallLike;
use Psy\CodeCleaner\ReturnTypePass;

class CalendarController extends Controller
{
    private static function add_days_to_calendar(Expert $expert, $date, $days_count)
    {
        $date->hour = 0;
        $date->minute = 0;
        $date->second = 0;
        for ($i = 0; $i < $days_count; $i++) {
            $day = new CalendarDay;
            $day->date = $date;
            $is_available = WorkDay::where('expert_id', $expert->id)
                ->where('day_of_week', $date->dayOfWeek)->first()->is_available;
            $day->first_av_st_1 = $expert->start_time_1 * $is_available;
            $day->end_time_1 = $expert->end_time_1 * $is_available;
            $day->first_av_st_2 = $expert->start_time_2 * $is_available;
            $day->end_time_2 = $expert->end_time_2 * $is_available;
            $day->setRelation('expert', $expert);
            $day->expert_id = $expert->id;
            if (!$day->save())
                return false;
            $date = $date->addDay();
        }
        return true;
    }

    public static function modify_calendar(Expert $expert)
    {
        $today = Carbon::today();
        $today->hour = 0;
        $today->minute = 0;
        $today->second = 0;
        $calendar = CalendarDay::where('expert_id', $expert->id);
        if ($calendar->count() == 0)
            if (!self::add_days_to_calendar($expert, $today, 30 * 3))
                return false;
        $other_far_day = Carbon::today()->addDays(12);
        $dumped_days = CalendarDay
            ::where('expert_id', $expert->id)
            ->whereDate('date', '<', $today);
        $new_days_count = $dumped_days->count();
        if ($new_days_count == 0)
            return true;
        $dumped_days->delete();
        $first_new_day_date = CalendarDay::latest()->where('expert_id', $expert->id)->first();
        if (is_null($first_new_day_date)) {
            if (!self::add_days_to_calendar($expert, $today, 30 * 3))
                return false;
        } else {
            $first_new_day_date->addDay();
            self::add_days_to_calendar($expert, $first_new_day_date, $new_days_count);
            return true;
        }
    }

    public static function period_1_is_available(CalendarDay $day) {
        return $day->first_av_st_1 < $day->end_time_1;
    }

    public static function period_2_is_available(CalendarDay $day) {
        return $day->first_av_st_2 < $day->end_time_2;
    }

    public function get_availability(Request $request)
    {
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);
        $calendar_days = CalendarDay::where('expert_id', $expert->id);
        self::modify_calendar($expert);
        $res = [];
        foreach ($calendar_days as $day) {
            dd(self::period_1_is_available($day));
            $temp = [$day->period_1_is_available(), $day->period_2_is_available()];
            array_push($res, $temp);
        }
        return response()->json($res);
    }

    public function reserve(Request $request)
    {
        $user = UsersController::find_user_or_fail($request->user_id);
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);
        $date = Carbon::parse($request->date);
        $date->hour = 0;
        $date->minute = 0;
        $date->second = 0;
        $period = $request->period;
        $appointment = new Appointment;
        $appointment->date = $date->format('y-m-d');
        $appointment->setRelation('user', $user);
        $appointment->setRelation('expert', $expert);
        $appointment->user_id = $user->id;
        $appointment->expert_id = $expert->id;
        if (!$appointment->save())
            return response()->json([
                'success' => false,
                'message' => 'could not save appointment'
            ]);
        $day = CalendarDay::where('expert_id', $expert->id)->whereDate('date', $date->format('y-m-d'))->first();
        if ($period == 1)
            $day->first_av_st_1++;
        else
            $day->first_av_st_2++;
        if (!$day->save())
            return response()->json([
                'success' => false,
                'message' => 'could not modify day of calendar'
            ]);
        return response()->json([
            'success' => true,
            'appointment booked successfully'
        ]);
    }
}