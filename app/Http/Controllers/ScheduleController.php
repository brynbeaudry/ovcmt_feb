<?php

namespace App\Http\Controllers;

use DateTime;
use App\Term;
use App\RoomsByDay;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function store(Request $req)
    {
        $saveDate = $req->schedule_date;
        $rooms = DB::table('rooms')
            ->select('room_id')
            ->get();
        $inputs = $req->all();
        $roomSlots = $this->getRoomsAndTimeslots();
        foreach($rooms as $room) {
            $roomId = $room->room_id;
            $roomUpdatesAm = $inputs[$roomSlots[$roomId]["am"]];
            $roomUpdatesPm = $inputs[$roomSlots[$roomId]["pm"]];
            $this->updateRoomByWeek($roomId, $saveDate, $roomUpdatesAm, $roomUpdatesPm);
        }
        //Send back to where schedule was saved
        $cdate = DateTime::createFromFormat('Y-m-d', $req->schedule_date);
        $year =$cdate->format('Y');
        $week = $cdate->format('W');
        $calendarDetails = $this->getCalendarDetails($cdate, $year, $week);
        $courseOfferings = $this->generateCourses($req->selected_term_id);
        //rooms by week just isn't getting the newly updated
        $roomsByWeek = $this->getScheduleByWeek($year, $week);
        $courseOfferingsSessions = $this->calculateDiff($courseOfferings);
        $term = DB::table('terms as t')
            ->join('intakes as i', 't.intake_id', '=', 'i.intake_id')
            ->where('term_id', $req->selected_term_id)
            ->first();
        return view('pages.dragDrop', compact('calendarDetails','courseOfferings', 'term', 'courseOfferingsSessions', 'roomsByWeek'));
    }

    public function getRoomsAndTimeslots()
    {
        return array('M1'=>array('am'=>'M1-am', 'pm'=>'M1-pm'),
                     'A1'=>array('am'=>'A1-am', 'pm'=>'A1-pm'),
                     'P1'=>array('am'=>'P1-am', 'pm'=>'P1-pm'),
                     'P2'=>array('am'=>'P2-am', 'pm'=>'P2-pm'));
    }

    /**
     * Given a week's schedule for a particular room, update schedule.
     * @param $roomId
     * @param $date
     * @param $amUpdates
     * @param $pmUpdates
     */
    public function updateRoomByWeek($roomId, $date, $amUpdates, $pmUpdates)
    {
        for($i = 0; $i<5; $i++) {
            $weekDay = date("Y-m-d", strtotime($date."+ $i days"));
            $am_crn = $amUpdates[$i] === "empty" ? null:$amUpdates[$i];
            $pm_crn = $pmUpdates[$i] === "empty" ? null:$pmUpdates[$i];
            $updated = RoomsByDay::where('room_id', $roomId)
                ->where('cdate', $weekDay)
                ->update(['am_crn'=>$am_crn,'pm_crn'=>$pm_crn]);
        }
    }

    public function generateCourses($term_id)
    {
        $courseofferings = DB::table('courses AS c')
            ->join('course_offerings AS co', 'c.course_id','=', 'co.course_id')
            //For instructor
            ->join('course_instructors AS ci', function($join)
            {
                $join->on('co.course_id', '=',  'ci.course_id');
                $join->on('co.instructor_id','=', 'ci.instructor_id');
                $join->on('co.intake_no','=', 'ci.intake_no');
            })
            ->join('instructors AS i', 'ci.instructor_id', '=', 'i.instructor_id')
            //for TA
            ->leftJoin('course_instructors AS ci_ta', function($join)
            {
                $join->on('co.course_id', '=',  'ci_ta.course_id');
                $join->on('co.ta_id','=', 'ci_ta.instructor_id');
                $join->on('co.intake_no','=', 'ci_ta.intake_no');
            })
            ->leftJoin('instructors AS i_ta', 'ci_ta.instructor_id', '=', 'i_ta.instructor_id')
            ->select('co.crn AS crn','co.course_id AS course_id', 'c.sessions_days AS sessions_days',
                     'co.instructor_id AS instructor_id', 'co.ta_id AS ta_id','i.first_name AS name', 'i_ta.first_name AS ta_name',
                     'c.color')
            ->where('co.term_id', $term_id)
            ->get();
            //dd($courseofferings);
        return $courseofferings;
    }

    public function calculateDiff($courseofferings) {
        $courseOfferingsDiff = array();
        foreach ($courseofferings as $offering) {
            $count = RoomsByDay::where('am_crn', $offering->crn)->count() +
                RoomsByDay::where('pm_crn', $offering->crn)->count();
            $courseOfferingsDiff[$offering->crn] = $offering->sessions_days - $count;
        }
        return $courseOfferingsDiff;
    }

    public function getScheduleByWeek($year, $week) {
        $amRoomsByWeek = $this->getScheduleByWeekQuery($year, $week, 'am');
        $pmRoomsByWeek = $this->getScheduleByWeekQuery($year, $week, 'pm');
        $allRoomsByWeek = $pmRoomsByWeek->union($amRoomsByWeek)->get();
        return $allRoomsByWeek;
    }

    public function getScheduleByWeekQuery($year, $week, $time)
    {
        return DB::table('courses AS c')
            ->join('course_offerings AS co', 'c.course_id', '=', 'co.course_id')
            ->join('course_instructors AS ci', function($join) {
                $join->on('co.course_id', '=',  'ci.course_id');
                $join->on('co.instructor_id','=', 'ci.instructor_id');
                $join->on('co.intake_no','=', 'ci.intake_no');
            })
            //for TA
            ->leftJoin('course_instructors AS ci_ta', function($join)
            {
                $join->on('co.course_id', '=',  'ci_ta.course_id');
                $join->on('co.ta_id','=', 'ci_ta.instructor_id');
                $join->on('co.intake_no','=', 'ci_ta.intake_no');
            })
            ->join('instructors AS i', 'ci.instructor_id', '=', 'i.instructor_id')
            ->leftJoin('instructors AS i_ta', 'ci_ta.instructor_id', '=', 'i_ta.instructor_id')
            ->join('terms AS t', 'co.term_id', '=', 't.term_id')
            ->join('intakes AS in', 't.intake_id','=', 'in.intake_id')
            ->join('rooms_by_days AS r', 'co.crn', '=', "r."."$time"."_crn")
            ->join('calendar_dates AS ca', 'r.cdate','=','ca.cdate')
            ->select('r.room_id AS room_id', 'r.cdate AS date', "r."."$time"."_crn AS crn",'co.course_id AS course_id',
                'i.first_name AS name', 'i_ta.first_name AS ta_name', 'in.start_date','in.intake_no','c.color',
                'ca.cdayOfWeek AS cdayOfWeek', DB::raw("'$time' AS time"))
            ->where([
                ["ca.cyear", $year],
                ["ca.cweek",intval($week)-1]
            ])
            ->whereNotNull("r."."$time"."_crn")
            ->whereIn('ca.cdayOfWeek',[2,3,4,5,6]);

    }

    public function getCalendarDetails($date, $year, $week)
    {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $calendar = array('month'=>$date->format('m'),
            'year'=>$date->format('Y'),
            'firstOfWeek'=>$dto->format('F d Y'),
            'goToDate'=>$dto->format('Y-m-d'), //MUST TAKE THIS FORMAT
            'mon'=>$dto->format('M d'));
        $dto->modify('+1 days');
        $calendar['tues']=$dto->format('M d');
        $dto->modify('+1 days');
        $calendar['wed']=$dto->format('M d');
        $dto->modify('+1 days');
        $calendar['thurs']=$dto->format('M d');
        $dto->modify('+1 days');
        $calendar['fri']=$dto->format('M d');
        //TODO find all holidays this week
        $calendar['holidays'] = DB::table('calendar_dates AS c')
            ->select('c.cdate', 'c.holidayDesc')
            ->where('cweek', $week)
            ->where('c.isWeekday', 1)
            ->get();
        return $calendar;
    }

    public function extractStartDate($term_id)
    {
        $term = DB::table('terms')
            ->select('*')
            ->where('term_id', $term_id)
            ->first();
        $cdate = DateTime::createFromFormat('Y-m-d', $term->term_start_date);
        return $cdate;
    }

    /**
     * Display schedule using the date selection tool.
     * @param Request $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function displayRoomsByWeek(Request $req) {
        if(!isset($req->schedule_select_date)) {
            $cdate = $this->extractStartDate($req->selected_term_id);
        } else {
            $cdate = DateTime::createFromFormat('Y-m-d', $req->schedule_select_date);
        }
        $year =$cdate->format('Y');
        $week = $cdate->format('W');
        $calendarDetails = $this->getCalendarDetails($cdate, $year, $week);
        $courseOfferings = $this->generateCourses($req->selected_term_id);
        $roomsByWeek = $this->getScheduleByWeek($year, $week);
        $courseOfferingsSessions = $this->calculateDiff($courseOfferings);
        $term = DB::table('terms as t')
            ->join('intakes as i', 't.intake_id', '=', 'i.intake_id')
            ->where('term_id', $req->selected_term_id)
            ->first();
        return view('pages.dragDrop', compact('calendarDetails','courseOfferings', 'courseOfferingsSessions', 'term', 'roomsByWeek'));
    }

    /**
     * Cannot see schedule without first selecting a term.
     * @return redirect to term select.
     */
    public function index() {
        return redirect()->action('ScheduleController@selectTerm');
    }

    /**
     * Provide terms for selection at term select page.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function selectTerm()
    {
        $terms = DB::table('terms AS t')
            ->join('intakes AS i', 't.intake_id', '=', 'i.intake_id')
            ->select('t.*', 'i.intake_no', 'i.start_date AS program_start')
            ->orderBy('i.start_date', 'DESC')
            ->get();
        return view('pages.selecttermschedule', compact('terms'));
    }

    public function propagate() {
        return view('pages.propagateschedule');
    }
}
