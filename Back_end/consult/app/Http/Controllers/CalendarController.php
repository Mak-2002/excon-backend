<?php

namespace App\Http\Controllers;

use App\Models\{CalendarDay, Expert};
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\CallLike;

class CalendarController extends Controller
{
    public static function add_days_to_calendar(Expert $expert, $date, $days_count) {
        for($i = 0 ; $i < $days_count ; $i++) {
            $item = new CalendarDay;
            $item->date = $date;
            $date->addDay();
            $item->setRelation('expert', $expert);
            $item->expert_id = $expert->id;
            if (!$item->save())
                return false;
        }
        //TODO: update hours
        return true;
    }

    public static function modify_calendar(Expert $expert) {
        $mean_date = Carbon::now();
        $calendar = CalendarDay::where('expert_id', $expert->id);
        if ($calendar->count() == 0)
            self::add_days_to_calendar($expert, $mean_date, 30*3);
        $dumped_days = CalendarDay::whereDate('date', '<', $mean_date);
        if (is_null($dumped_days))
            return; // TODO
        $new_days_count = $dumped_days->count();
        $dumped_days->delete();
        $first_new_day_date = CalendarDay::where('expert_id', $expert->id)->latest->first()->date->addDay();
        self::add_days_to_calendar($expert, $first_new_day_date, $new_days_count);
        self::update_hours($expert);
    }
    private function in_service_time($hour, Expert $expert) {
        $st1 = $expert->schedule->first()->start_time_1;
        $et1 = $expert->schedule->first()->end_time_1;
        $st2 = $expert->schedule->first()->start_time_2;
        $et2 = $expert->schedule->first()->end_time_2;
        return ($hour>=$st1 && $hour<$et1) || ($hour>=$st2 && $hour<$et2) ;
    }

    public static function update_hours(Expert $expert) {
        $calendar = CalendarDay::where('expert_id', $expert->id);
        foreach( $calendar as $day) {
            
        }
    }
}
