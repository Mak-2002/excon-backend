<?php

namespace App\Http\Controllers;

use App\Models\{CalendarDay, CalendarDayHour, Expert, WorkDay};
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

    public static function add_days_to_calendar(Expert $expert, $date, $days_count)
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
        $mean_date = Carbon::now();
        $calendar = CalendarDay::where('expert_id', $expert->id);
        if ($calendar->count() == 0)
            self::add_days_to_calendar($expert, $mean_date, 30 * 3);
        $dumped_days = CalendarDay::whereDate('date', '<', $mean_date);
        if (is_null($dumped_days))
            return; // TODO
        $new_days_count = $dumped_days->count();
        foreach ($dumped_days as $day) {
            $hours = CalendarDayHour::whereHas('calendar_day', $day);
            if ($hours->count() > 0)
                $hours->delete();
        }
        $dumped_days->delete();
        $first_new_day_date = CalendarDay::where('expert_id', $expert->id)->latest->first()->date->addDay();
        self::add_days_to_calendar($expert, $first_new_day_date, $new_days_count);
        self::update_hours($expert);
    }
    private static function in_service_time($hour, Expert $expert)
    {
        $entry = $expert->schedule->first();
        $st1 = $entry->start_time_1;
        $et1 = $entry->end_time_1;
        $st2 = $entry->start_time_2;
        $et2 = $entry->end_time_2;
        return ($hour >= $st1 && $hour < $et1) || ($hour >= $st2 && $hour < $et2);
    }

    public static function update_hours(Expert $expert)
    {
        $calendar = CalendarDay::where('expert_id', $expert->id);
        $entry = $expert->schedule->first();
        $st1 = $entry->start_time_1;
        $et1 = $entry->end_time_1;
        $st2 = $entry->start_time_2;
        $et2 = $entry->end_time_2;
        foreach ($calendar as $day) {
            $is_available = WorkDay
                ::whereHas('expert', $expert)
                ->where('day_of_week', $day->date->dayOfWeek)
                ->first()->is_available;
            if (!$is_available) {
                $st1 = 0;
                $et1 = 0;
                $st2 = 0;
                $et2 = 0;
            }
            $day->available_hours_count = $et1 - $st1 + $et2 - $st2;
            foreach ($day->hours as $item) {
                if ($item->occupied) {
                    $in_service_time = self::in_service_time($item->hour, $expert);
                    if ($item->occupied)
                        if ($in_service_time)
                            $day->available_hours_count = max(0, $day->available_hours_count - 1);
                        else
                            $item->state = $in_service_time - 1;
                }
                if (!$item->save())
                    return false;
            }
            if (!$day->save())
                return false;
        }
        return true;
    }

}