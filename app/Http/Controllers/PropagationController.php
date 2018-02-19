<?php

namespace App\Http\Controllers;

use App\CalendarDate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\RoomsByDay;
use Illuminate\Support\Facades\DB;

class PropagationController extends Controller
{
    public function extend(Request $req) {
        if (isset($req->week_monday) && isset($req->weeks)) {
            $weeks = trim($req->weeks);
            $holidays = $this->getHolidayArray($req->week_monday, $weeks);
            $weekstart = Carbon::createFromFormat('Y-m-d', $req->week_monday);
            $weekend = Carbon::createFromFormat('Y-m-d', $req->week_monday);
            $weekend->addDays(4);
            $weeklyschedule = RoomsByDay::whereBetween('cdate', array($weekstart->toDateString(), $weekend->toDateString()))
                ->get();
            for($i = 1; $i <= $weeks; $i++) { // each week
                DB::beginTransaction(); // begin transaction for the week
                foreach ($weeklyschedule as $roomday) { //each day
                    $date = Carbon::createFromFormat('Y-m-d', $roomday->cdate);
                    $date->addDays(7 * $i);
                    if(!empty($roomday->am_crn)) { //am course exists, check to see if you have sessions left
                        if($this->hasSessionsLeft($roomday->am_crn) <= 0){
                            $status = array('count' => $i, 'date' => $date->toDateString(), 'crn' => $roomday->am_crn);
                            DB::rollback();
                            return view('pages.onPropFinish', compact('status'));
                        }
                    }
                    if(!empty($roomday->pm_crn)) { //pm course exists, check to see if you have sessions left
                        if($this->hasSessionsLeft($roomday->pm_crn) <= 0){
                            $status = array('count' => $i, 'date' => $date->toDateString(), 'crn' => $roomday->pm_crn);
                            DB::rollback();
                            return view('pages.onPropFinish', compact('status'));
                        }
                    }
                    if(!in_array($date->toDateString(), $holidays)) {
                        RoomsByDay::where('room_id', $roomday->room_id)
                            ->where('cdate', $date->toDateString())
                            ->update(['am_crn' => $roomday->am_crn, 'pm_crn' => $roomday->pm_crn]);
                    }
                }
                DB::commit(); //commit transaction for the week
            }
            $status = array("message" => "worked", "weeks" => $weeks);
            return view('pages.onPropFinish', compact('status'));
        }
    }

    public function hasSessionsLeft($crn) {
        $count = RoomsByDay::where('am_crn', $crn)->count() +
            RoomsByDay::where('pm_crn', $crn)->count();
        $coursecount = DB::table('courses AS c')
            ->join('course_offerings AS co', 'c.course_id', '=', 'co.course_id')
            ->where('co.crn', $crn)
            ->pluck('c.sessions_days')->first();
        return $coursecount - $count;
    }

    public function getHolidayArray($week_monday, $weeks) {
        $weekstart = Carbon::createFromFormat('Y-m-d', $week_monday);
        $weekend = Carbon::createFromFormat('Y-m-d', $week_monday);
        $weekend->addDays(7 * ($weeks + 1)); // + 1 week to account for current week
        $holidays = CalendarDate::whereBetween('cdate', array($weekstart->toDateString(), $weekend->toDateString()))
            ->where('isHoliday', 1)
            ->pluck('cdate')
            ->toArray();
        return $holidays;
    }
}
