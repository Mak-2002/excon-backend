<?php

namespace App\Http\Controllers;

use App\Models\{Appointment, CalendarDay, CalendarDayHour, Expert, WorkDay};
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\CallLike;
use Psy\CodeCleaner\ReturnTypePass;

class CalendarController extends Controller
{
    public static function create_day_hours(CalendarDay $day, Expert $expert)
    {
        for ($i = 0; $i < 24; $i++) {
            $hour = new CalendarDayHour;
            $hour->setRelation('calendar_day', $day);
            $hour->calendar_day_id = $day->id;
            $hour->hour = $i;
            if (!$hour->save())
                return false;
        }
        return true;
    }

    private static function add_days_to_calendar(Expert $expert, $date, $days_count)
    {
        for ($i = 0; $i < $days_count; $i++) {
            $item = new CalendarDay;
            $item->date = $date;
            $date->addDay();
            $item->setRelation('expert', $expert);
            $item->expert_id = $expert->id;
            if (!$item->save())
                return false;
            self::create_day_hours($item, $expert);
        }
        //TODO: update hours
        return true;
    }

    public static function modify_calendar(Expert $expert)
    {
        $today = Carbon::now();
        $today->hour = 0;
        $today->minute = 0;
        $today->second = 0;
        $calendar = CalendarDay::whereHas('expert', $expert);
        if (is_null($calendar))
            self::add_days_to_calendar($expert, $today, 30 * 3);
        $dumped_days = CalendarDay::whereDate('date', '<', $today);
        if (is_null($dumped_days))
            return; // TODO
        $new_days_count = $dumped_days->count();
        foreach ($dumped_days as $day) {
            $hours = CalendarDayHour::whereHas('calendar_day', $day);
            if (!is_null($hours))
                $hours->delete();
        }
        $dumped_days->delete();
        $first_new_day_date = CalendarDay::whereHas('expert', $expert)->latest->first()->date->addDay();
        self::add_days_to_calendar($expert, $first_new_day_date, $new_days_count);
        self::update_hours($expert);
    }
    private static function in_service_time($hour, Expert $expert)
    {
        $st1 = $expert->start_time_1;
        $et1 = $expert->end_time_1;
        $st2 = $expert->start_time_2;
        $et2 = $expert->end_time_2;
        return ($hour >= $st1 && $hour < $et1) || ($hour >= $st2 && $hour < $et2);
    }

    public static function update_hours(Expert $expert)
    {
        $calendar = CalendarDay::where('expert_id', $expert->id);
        $entry = $expert->schedule->first();
        foreach ($calendar as $day) {
            $day_schedule = WorkDay
                ::whereHas('expert', $expert)
                ->where('day_of_week', $day->date->dayOfWeek)
                ->first();
            $is_available = $day_schedule->is_available;
            $day->available_hours_count =
                $expert->end_time_1 - $expert->start_time_1 +
                $expert->end_time_2 - $expert->start_time_2;
            $day->available_hours_count *= $is_available;
            foreach ($day->hours as $hour_entity) {
                $in_service_time = self::in_service_time($hour_entity->hour, $expert) * $is_available;
                if ($hour_entity->occupied) {
                    if ($in_service_time)
                        $day->available_hours_count--;
                } else
                    $hour_entity->state = $in_service_time - 1;
                if (!$hour_entity->save())
                    return false;
            }
            if (!$day->save())
                return false;

        }
        return true;
    }

    public function get_availability(Request $request)
    {
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);
        self::modify_calendar($expert);
        $res = [];
        foreach (CalendarDay::whereHas('expert', $expert) as $day)
            array_push($res, $day->is_available);
        return response()->json([$res]);
    }

    public function get_available_hours(Request $request)
    {
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);
        $date = Carbon::parse($request->date);
        $date->hour = 0;
        $date->minute = 0;
        $date->second = 0;
        $day = CalendarDay::whereHas('expert', $expert)->whereDate('date', $date);
        $available_hours = CalendarDayHour::whereHas('calendar_day', $day)->where('state', 0);
        $res = [];
        foreach ($available_hours as $hour)
            array_push($res, $hour->hour);
        return response()->json([$res]);
    }

    public function reserve(Request $request) 
    {
        $user = UsersController::find_user_or_fail($request->user_id);
        $expert = ExpertsController::find_expert_by_user_id_or_fail($request->expert_id);
        $date = Carbon::parse($request->date);
        $appointment = new Appointment;
        $appointment->date = $date->format('y-m-d');
        $appointment->setRelation('user', $user);
        $appointment->setRelation('expert', $expert);

    }
}