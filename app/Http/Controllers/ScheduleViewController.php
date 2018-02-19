<?php

namespace App\Http\Controllers;
use DB;
use App\Instructor;
use App\Term;
use App\Intake;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;

class ScheduleViewController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all dates in a calendar month.
     * @param $days
     * @return array of weeks
     */
    public function getCalendar($days)
    {
        $weeks = array();
        $tmpWeek = array();
        foreach ($days as $day) {
            $date = DateTime::createFromFormat('Y-m-d', $day->cdate);
            array_push($tmpWeek, $date->format('j'));
            if ($day->cdayOfWeek == 6) { //if it's sunday, push
                array_push($weeks, $tmpWeek);
                $tmpWeek = array(); //clear tmp
            }
        }
        if (!empty($tmpWeek)) {
            array_push($weeks, $tmpWeek);
        }
        return $weeks;
    }

    /**
     * Generate the entire schedule by month given an intake and a date.
     * @param $date
     * @return array
     */
    public function getScheduleByMonth($date, $intake)
    {
        $terms = Term::where('intake_id', $intake)
            ->select('term_id')
            ->get();
        $amCourses = DB::table('courses AS c')
            ->join('course_offerings AS co', 'c.course_id', '=', 'co.course_id')
            ->join('course_instructors AS ci', function($join) {
                $join->on('co.course_id', '=',  'ci.course_id');
                $join->on('co.instructor_id','=', 'ci.instructor_id');
                $join->on('co.intake_no','=', 'ci.intake_no');
            })
            ->join('instructors AS i', 'ci.instructor_id', '=', 'i.instructor_id')
            ->join('rooms_by_days AS r', 'co.crn', '=', "r.am_crn")
            ->join('calendar_dates AS ca', 'r.cdate','=','ca.cdate')
            ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color', 'i.first_name')
            ->whereMonth('r.cdate', $date->format('n'))
            ->whereYear('r.cdate', $date->format('Y'))
            ->whereIn('co.term_id', $terms)
            ->distinct()
            ->get();
        $pmCourses = DB::table('courses AS c')
            ->join('course_offerings AS co', 'c.course_id', '=', 'co.course_id')
            ->join('course_instructors AS ci', function($join) {
                $join->on('co.course_id', '=',  'ci.course_id');
                $join->on('co.instructor_id','=', 'ci.instructor_id');
                $join->on('co.intake_no','=', 'ci.intake_no');
            })
            ->join('instructors AS i', 'ci.instructor_id', '=', 'i.instructor_id')
            ->join('rooms_by_days AS r', 'co.crn', '=', "r.pm_crn")
            ->join('calendar_dates AS ca', 'r.cdate','=','ca.cdate')
            ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color', 'i.first_name')
            ->whereMonth('r.cdate', $date->format('n'))
            ->whereYear('r.cdate', $date->format('Y'))
            ->whereIn('co.term_id', $terms)
            ->distinct()
            ->get();
        return array('am_courses'=>$amCourses, 'pm_courses'=>$pmCourses);
    }

    public function getTAScheduleByMonth($date, $instructor)
    {
      //do not show absent courses
      $am_absentCourses = DB::table('rooms_by_days AS r')
          ->join('substitutions AS s','r.am_sub', '=', 's.id')
          ->join('course_offerings AS co', 's.sub_crn', '=', 'co.crn')
          ->join('courses AS c', 'co.course_id', '=', 'c.course_id')
          ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
          ->whereMonth('r.cdate', $date->format('n'))
          ->whereYear('r.cdate', $date->format('Y'))
          ->where('s.instructor_id', $instructor)
          ->get()->keyBy(function($item){
              return "$item->cdate-AM";
          });
      //dd($am_absentCourses, $date, $instructor);
      //remove absent courses from courses returned
      $pm_absentCourses = DB::table('rooms_by_days AS r')
          ->join('substitutions AS s','r.pm_sub', '=', 's.id')
          ->join('course_offerings AS co', 's.sub_crn', '=', 'co.crn')
          ->join('courses AS c', 'co.course_id', '=', 'c.course_id')
          ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
          ->whereMonth('r.cdate', $date->format('n'))
          ->whereYear('r.cdate', $date->format('Y'))
          ->where('s.instructor_id', $instructor)
          ->get()->keyBy(function($item){
              return "$item->cdate-PM";
          });

      $amCourses = DB::table('rooms_by_days AS r')
          ->leftjoin('course_offerings AS co', 'r.am_crn', '=', 'co.crn')
          ->join('course_instructors AS ci', function($join) {
              $join->on('co.course_id', '=',  'ci.course_id');
              $join->on('co.ta_id','=', 'ci.instructor_id');
              $join->on('co.intake_no','=', 'ci.intake_no');
          })
          ->join('courses AS c', 'ci.course_id', '=', 'c.course_id')
          ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
          ->whereMonth('r.cdate', $date->format('n'))
          ->whereYear('r.cdate', $date->format('Y'))
          ->where('co.ta_id', $instructor)
          ->get()->keyBy(function($item){
              return "$item->cdate-AM";
          });
      //dd($amCourses);
      //add in courses where you are subbing.
      $amSubCourses =  DB::table('rooms_by_days AS r')
          ->join('substitutions AS s', 'r.am_sub', '=', 's.id')
          ->join('course_offerings AS co', 's.sub_crn', '=', 'co.crn')
          ->join('courses AS c', 'co.course_id', '=', 'c.course_id')
          ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
          ->where('s.sub_ta_id', $instructor)
          ->whereMonth('r.cdate', $date->format('n'))
          ->whereYear('r.cdate', $date->format('Y'))
          ->get()->keyBy(function($item){
              return "$item->cdate-AM";
          });
      //$amCourses = $amSubCourses->union($amCourses);
      //dd($amSubCourses, $instructor, $date->format('n'),$date->format('Y'));
      $pmCourses = DB::table('rooms_by_days AS r')
          ->leftjoin('course_offerings AS co', 'r.pm_crn', '=', 'co.crn')
          ->join('course_instructors AS ci', function($join) {
              $join->on('co.course_id', '=',  'ci.course_id');
              $join->on('co.ta_id','=', 'ci.instructor_id');
              $join->on('co.intake_no','=', 'ci.intake_no');
          })
          ->join('courses AS c', 'ci.course_id', '=', 'c.course_id')
          ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
          ->whereMonth('r.cdate', $date->format('n'))
          ->whereYear('r.cdate', $date->format('Y'))
          ->where('co.ta_id', $instructor)
          ->get()->keyBy(function($item){
              return "$item->cdate-PM";
          });
      $pmSubCourses =  DB::table('rooms_by_days AS r')
          ->join('substitutions AS s', 'r.pm_sub', '=', 's.id')
          ->join('course_offerings AS co', 's.sub_crn', '=', 'co.crn')
          ->join('courses AS c', 'co.course_id', '=', 'c.course_id')
          ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
          ->where('s.sub_ta_id', $instructor)
          ->whereMonth('r.cdate', $date->format('n'))
          ->whereYear('r.cdate', $date->format('Y'))
          ->get()->keyBy(function($item){
              return "$item->cdate-PM";
          });
      $pmCourses = $pmSubCourses->union($pmCourses);
      //if absent courses match any courses returned, take them out
      //$pmCourses = $pmCourses->diffKeys($pm_absentCourses);
      //or if absent courses match any courses returned
      //dd($pm_absentCourses);
      foreach ($pmCourses as $key => $value) {
          foreach ($pm_absentCourses as $key_abs => $value) {
            //if the keys are the same
            if(!strcmp($key, $key_abs)){
              $pmCourses[$key]->course_id = 'TIME OFF';
              $pmCourses[$key]->color = '#787878';
            }
        }
      }

      $amCourses = $amSubCourses->union($amCourses);
      //$amCourses = $amCourses->diffKeys($am_absentCourses);
      //if absent courses match any courses returned, take them out
      //$pmCourses = $pmCourses->diffKeys($pm_absentCourses);
      //or if absent courses match any courses returned
      foreach ($amCourses as $key => $value) {
          foreach ($am_absentCourses as $key_abs => $value) {
            //if the keys are the same
            if(!strcmp($key, $key_abs)){
              $amCourses[$key]->course_id = 'TIME OFF';
              $amCourses[$key]->color = '#787878';
            }
        }
      }
      //dd(array('am_courses'=>$amCourses, 'pm_courses'=>$pmCourses));
      return collect(['am_courses'=>$amCourses, 'pm_courses'=>$pmCourses]);
    }

    public function getInstructorScheduleByMonth($date, $instructor)
    {
        //do not show absent courses
        $am_absentCourses = DB::table('rooms_by_days AS r')
            ->join('substitutions AS s','r.am_sub', '=', 's.id')
            ->join('course_offerings AS co', 's.sub_crn', '=', 'co.crn')
            ->join('courses AS c', 'co.course_id', '=', 'c.course_id')
            ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
            ->whereMonth('r.cdate', $date->format('n'))
            ->whereYear('r.cdate', $date->format('Y'))
            ->where('s.instructor_id', $instructor)
            ->get()->keyBy(function($item){
                return "$item->cdate-AM";
            });
        //dd($am_absentCourses, $date, $instructor);
        //remove absent courses from courses returned
        $pm_absentCourses = DB::table('rooms_by_days AS r')
            ->join('substitutions AS s','r.pm_sub', '=', 's.id')
            ->join('course_offerings AS co', 's.sub_crn', '=', 'co.crn')
            ->join('courses AS c', 'co.course_id', '=', 'c.course_id')
            ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
            ->whereMonth('r.cdate', $date->format('n'))
            ->whereYear('r.cdate', $date->format('Y'))
            ->where('s.instructor_id', $instructor)
            ->get()->keyBy(function($item){
                return "$item->cdate-PM";
            });

        $amCourses = DB::table('rooms_by_days AS r')
            ->leftjoin('course_offerings AS co', 'r.am_crn', '=', 'co.crn')
            ->join('course_instructors AS ci', function($join) {
                $join->on('co.course_id', '=',  'ci.course_id');
                $join->on('co.instructor_id','=', 'ci.instructor_id');
                $join->on('co.intake_no','=', 'ci.intake_no');
            })
            ->join('courses AS c', 'ci.course_id', '=', 'c.course_id')
            ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
            ->whereMonth('r.cdate', $date->format('n'))
            ->whereYear('r.cdate', $date->format('Y'))
            ->where('co.instructor_id', $instructor)
            ->get()->keyBy(function($item){
                return "$item->cdate-AM";
            });
        //dd($amCourses);
        //add in courses where you are subbing.
        $amSubCourses =  DB::table('rooms_by_days AS r')
            ->join('substitutions AS s', 'r.am_sub', '=', 's.id')
            ->join('course_offerings AS co', 's.sub_crn', '=', 'co.crn')
            ->join('courses AS c', 'co.course_id', '=', 'c.course_id')
            ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
            ->where('s.sub_instructor_id', $instructor)
            ->whereMonth('r.cdate', $date->format('n'))
            ->whereYear('r.cdate', $date->format('Y'))
            ->get()->keyBy(function($item){
                return "$item->cdate-AM";
            });
        //$amCourses = $amSubCourses->union($amCourses);
        //dd($amSubCourses, $instructor, $date->format('n'),$date->format('Y'));
        $pmCourses = DB::table('rooms_by_days AS r')
            ->leftjoin('course_offerings AS co', 'r.pm_crn', '=', 'co.crn')
            ->join('course_instructors AS ci', function($join) {
                $join->on('co.course_id', '=',  'ci.course_id');
                $join->on('co.instructor_id','=', 'ci.instructor_id');
                $join->on('co.intake_no','=', 'ci.intake_no');
            })
            ->join('courses AS c', 'ci.course_id', '=', 'c.course_id')
            ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
            ->whereMonth('r.cdate', $date->format('n'))
            ->whereYear('r.cdate', $date->format('Y'))
            ->where('co.instructor_id', $instructor)
            ->get()->keyBy(function($item){
                return "$item->cdate-PM";
            });
        $pmSubCourses =  DB::table('rooms_by_days AS r')
            ->join('substitutions AS s', 'r.pm_sub', '=', 's.id')
            ->join('course_offerings AS co', 's.sub_crn', '=', 'co.crn')
            ->join('courses AS c', 'co.course_id', '=', 'c.course_id')
            ->select('r.room_id', 'c.course_id', 'r.cdate', 'c.color')
            ->where('s.sub_instructor_id', $instructor)
            ->whereMonth('r.cdate', $date->format('n'))
            ->whereYear('r.cdate', $date->format('Y'))
            ->get()->keyBy(function($item){
                return "$item->cdate-PM";
            });
        //dd($am_absentCourses, $pm_absentCourses);
        $pmCourses = $pmSubCourses->union($pmCourses);
        //if absent courses match any courses returned, take them out
        //$pmCourses = $pmCourses->diffKeys($pm_absentCourses);
        //or if absent courses match any courses returned
        //dd($pm_absentCourses);
        foreach ($pmCourses as $key => $value) {
            foreach ($pm_absentCourses as $key_abs => $value) {
              //if the keys are the same
              if(!strcmp($key, $key_abs)){
                $pmCourses[$key]->course_id = 'TIME OFF';
                $pmCourses[$key]->color = '#787878';
              }
          }
        }



        $amCourses = $amSubCourses->union($amCourses);
        //$amCourses = $amCourses->diffKeys($am_absentCourses);
        //if absent courses match any courses returned, take them out
        //$pmCourses = $pmCourses->diffKeys($pm_absentCourses);
        //or if absent courses match any courses returned
        foreach ($amCourses as $key => $value) {
            foreach ($am_absentCourses as $key_abs => $value) {
              //if the keys are the same
              if(!strcmp($key, $key_abs)){
                $amCourses[$key]->course_id = 'TIME OFF';
                $amCourses[$key]->color = '#787878';
              }
          }
        }
        //dd(array('am_courses'=>$amCourses, 'pm_courses'=>$pmCourses));
        return collect(['am_courses'=>$amCourses, 'pm_courses'=>$pmCourses]);
    }

    /**
     * Provide all intakes for schedule view selection.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function selectStudent()
    {
        $intakes = DB::table('intakes as i')
            ->select('i.*', DB::raw('YEAR(i.start_date) AS start_year'))
            ->get();
        return view('pages.selectstudentschedule', compact('intakes'));
    }
	

    /**
     * Provide all intakes for schedule view selection.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function selectInstructor()
    {
        $instructors = DB::table('instructors as i')
            ->select('i.*')
            ->get();
        return view('pages.selectinstructorschedule', compact('instructors'));
    }

    public function studentIndex(Request $req)
    {
        if(isset($req->schedule_starting_date)) {
            //if it's set then we should go to this date
            $schedDate = DateTime::createFromFormat('Y-m-d', $req->schedule_starting_date);
        } else {
            //Default to current week otherwise
            $schedDate = new DateTime(null, new DateTimeZone('America/Vancouver'));
        }
        $calendar = DB::table('calendar_dates')
            ->where('cmonth', $schedDate->format('n'))
            ->where('cyear', $schedDate->format('Y'))
            ->where('isWeekday', 1)
            ->get();
        $weeks = $this->getCalendar($calendar);
        $courses = $this->getScheduleByMonth($schedDate, $req->schedule_intake);
        //dd($courses);
        $intake_info = Intake::where('intake_id', $req->schedule_intake)
            ->select('intake_no', 'start_date')
            ->first();
        $intake_info->start_date = DateTime::createFromFormat('Y-m-d', $intake_info->start_date);
        $details = array('intake_id'=>$req->schedule_intake, 'schedule_date'=>$schedDate, 'intake_info'=>$intake_info);
        return view('pages.schedulestudent', compact('weeks', 'courses','details'));
    }

    public function instructorIndex(Request $req)
    {
        if(isset($req->schedule_starting_date)) {
            //if it's set then we should go to this date
            $schedule_date = DateTime::createFromFormat('Y-m-d', $req->schedule_starting_date);
        } else {
            //Default to current week otherwise
            $schedule_date = new DateTime(null, new DateTimeZone('America/Vancouver'));
        }
        $calendar = DB::table('calendar_dates')
            ->where('cmonth', $schedule_date->format('n'))
            ->where('cyear', $schedule_date->format('Y'))
            ->where('isWeekday', 1)
            ->get();
        $weeks = $this->getCalendar($calendar);
        /*in here you can edit their schedule if someone is covering for them at this time.*/
        $instructorCourses = $this->getInstructorScheduleByMonth($schedule_date, $req->schedule_instructor);
        $taCourses = $this->getTAScheduleByMonth($schedule_date, $req->schedule_instructor);
        //dd($instructorCourses, $taCourses, $instructorCourses['am_courses']->union($taCourses['am_courses']));
        $courses = array();
        $courses['am_courses'] = $instructorCourses['am_courses']->union($taCourses['am_courses']);
        $courses['pm_courses'] = $instructorCourses['pm_courses']->union($taCourses['pm_courses']);
        //$courses = $courses->toArray();
        //dd($courses, $instructorCourses, $taCourses);

        $instructor = DB::table('instructors')
            ->where('instructor_id', $req->schedule_instructor)
            ->first();
        //dd($weeks, $courses, $instructor, $schedule_date);
        return view('pages.scheduleinstructor', compact('weeks', 'courses', 'instructor', 'schedule_date'));
    }
    
    public function intakeName($intake)
    {
        $intake_date = DateTime::createFromFormat('Y-m-d', $intake->start_date);
        
        $intake_year = $intake_date->format('Y');
        $intake_letter = $intake->intake_no;
        
        if ($intake_letter == 'A')
        {
            $intake_year += 2;
        }
        else if ($intake_letter == 'B')
        {
            $intake_year += 1;
        }
        // if neither are true this function needs to be redone anyway
        
        $intake_string = $intake_year . $intake_letter;
        
        return $intake_string;
    }
    
    public function masterindex(Request $req)
    {
        if(isset($req->schedule_starting_date))
        {
            //if it's set then we should go to this date
            $schedule_date = DateTime::createFromFormat('Y-m-d', $req->schedule_starting_date);
        } else
        {
            //Default to current week otherwise
            $schedule_date = new DateTime(null, new DateTimeZone('America/Vancouver'));
        }
        
        $calendar = DB::table('calendar_dates')
            ->where('cmonth', $schedule_date->format('n'))
            ->where('cyear', $schedule_date->format('Y'))
            ->where('isWeekday', 1)
            ->get();
        $weeks = $this->getCalendar($calendar);
        
        // get a listing of every intake
        $intakes = DB::table('intakes')
            ->get();
        
        // get a listing of courses in this month for each intake
        $courses = array();
        foreach($intakes as $intake)
        {
            $intakeCourses = $this->getScheduleByMonth($schedule_date, $intake->intake_id);
            // get a presentable name for the intake
            $intakeCourses['intake_name'] = self::intakeName($intake);
            // check if intake has courses in this month before adding it to the courses. do NOT add if empty
            if ($intakeCourses['am_courses']->count() > 0 || $intakeCourses['pm_courses']->count() > 0)
            {
                $courses[$intake->intake_id] = $intakeCourses;
            }
        }
        
        return view('pages.schedulemaster', compact('weeks', 'courses', 'schedule_date'));
    }
}
