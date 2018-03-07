<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\RoomsByDay;
use App\Substitution;

class SubstitutionController extends Controller
{
      private function queryOfferingsByInstructorByDateRange($start_date,$end_date, $instructor_id){
        $instructor_offerings = DB::table('rooms_by_days AS rbd')
          ->join('course_offerings AS co', function($join){
              $join->on('rbd.am_crn', '=', 'co.crn')->orOn('rbd.pm_crn', '=', 'co.crn');
            })
          ->where('co.instructor_id','=', $instructor_id)
          ->whereBetween('rbd.cdate', array($start_date, $end_date))
          /* It's possible you may want all the columns from rooms_by_days, so you can see if you've already made a substitution for that timeslot */
          ->select('co.*', 'rbd.*')
          ->orderBy('co.crn')
          ->get();
        $ta_offerings = DB::table('rooms_by_days AS rbd')
          ->join('course_offerings AS co', function($join){
              $join->on('rbd.am_crn', '=', 'co.crn')->orOn('rbd.pm_crn', '=', 'co.crn');
            })
          ->where('co.ta_id','=', $instructor_id)
          ->whereBetween('rbd.cdate', array($start_date, $end_date))
          /*make is work for tas aswell, using OR*/
          ->select('co.*', 'rbd.*')
          ->orderBy('co.crn')
          ->get();
        $result = $instructor_offerings->merge($ta_offerings);
        return $result;
      }

      /*cdtae is a collection of key vals where the date is the key and the am/pm is the val*/
      private function queryInstructorsbyAvailibility($instructor_id, $cdate, $instructor_type, $course_id){
        /*look for all instructors where they are not teaching a course on those days, during the slot to be replaced*/
        $whereConditions_avail = collect();
        $dates = collect();


        /*Needs to be fixed to take Substitutes and TAs into account*/

        //make the condition for checking availablility doring the date and slot
        foreach ($cdate as $dateslot) {
          list($date, $slot) = explode(",", $dateslot);
          $carbon_date = Carbon::createFromFormat('Y-m-d', $date, 'America/Vancouver');
          $day = strtolower($carbon_date->format('l'));
          if($day== 'thursday'){
            $day= 'thurs';
          }else if ($day== 'tuesday') {
            $day = 'tues';
          }else{
            $day = substr($day, 0 , 3);
          }
          $column = $day . '_' . strtolower($slot);
          $condition = ['ia.'. $column, '=', 1];
          if(!($whereConditions_avail->contains($condition)))
            $whereConditions_avail->push($condition);
          $dates->push($date);

        }

        //gets available instructors during the dates and slots of absense.
        if($instructor_type=='Instructor'){ $type_col = 'instructor_id'; }else{ $type_col = 'ta_id'; }
        $available_instructors_by_date_slot = DB::table('instructors AS i')
        ->join('instruct_avails As ia','i.instructor_id', '=', 'ia.instructor_id')
        ->where($whereConditions_avail->toArray())
        ->where('i.instructor_id', '!=', $instructor_id)
        ->select('i.*')
        ->get();

        //rooms by days for that date where the insturcot in question is going to miss a course paired with the course offerings of the am and pm timeslots. rooms by dates is all the course offerings on that day, where the instructor absent isn't schedule(because it would be redundant to check those.). We'll be excluding replacements based on the people who are teaching during these times.

        //these are the courses during absense days. They contain details about busy teacher
        $am_courses_during_absense_days = DB::table('rooms_by_days as rbd')
          ->join('course_offerings AS co','rbd.am_crn', '=', 'co.crn')
          ->whereIn('rbd.cdate', $dates->toArray())
          ->where("co.$type_col", '!=', $instructor_id)
          ->select('co.*', 'rbd.*')
          ->get();
        //dd($am_courses_during_absense_days);

        //get the ones with substitutions
            $am_subs_during_absense_days = collect();
            $am_subs_during_absense_days = $am_courses_during_absense_days->reject(function($item,$key){
                      return $item->am_sub ===null;
            });
            $am_subs_during_absense_days =$am_subs_during_absense_days->pluck('am_sub');

            $am_sub_courses_during_absense_days = DB::table('rooms_by_days AS rbd')
            ->join('course_offerings AS co','rbd.am_crn', '=', 'co.crn')
            ->join('substitutions AS s',"s.id", "=", "rbd.am_sub")
            ->whereIn("rbd.am_sub", $am_subs_during_absense_days->toArray())
            ->where("co.$type_col", '!=', $instructor_id)
            ->select('co.crn', 'co.term_id', 'co.course_id', 'co.intake_no', 'rbd.*', "s.sub_instructor_id AS instructor_id", "s.sub_ta_id AS ta_id")
            ->get();


        $pm_courses_during_absense_days = DB::table('rooms_by_days as rbd')
          ->join('course_offerings AS co','rbd.pm_crn', '=', 'co.crn')
          ->whereIn('rbd.cdate', $dates->toArray())
          ->where("co.$type_col", '!=', $instructor_id)
          ->select('co.*', 'rbd.*')
          ->get();

            $pm_subs_during_absense_days = collect();
            $pm_subs_during_absense_days = $pm_courses_during_absense_days->reject(function($item,$key){
                      return $item->pm_sub ===null;
            });
            $pm_subs_during_absense_days =$pm_subs_during_absense_days->pluck('pm_sub');
            //dd($pm_subs_during_absense_days);

            //same query but
            //DB::enableQueryLog();
            $pm_sub_courses_during_absense_days = DB::table('rooms_by_days AS rbd')
            ->join('course_offerings AS co','rbd.pm_crn', '=', 'co.crn')
            ->join('substitutions AS s',"s.id", "=", "rbd.pm_sub")
            ->whereIn("rbd.pm_sub", $pm_subs_during_absense_days->toArray())
            ->where("co.$type_col", '!=', $instructor_id)
            ->select('co.crn', 'co.term_id', 'co.course_id', 'co.intake_no', 'rbd.*', "s.sub_instructor_id AS instructor_id", "s.sub_ta_id AS ta_id")
            ->get();
            //dd(DB::getQueryLog());
            //dd($pm_sub_courses_during_absense_days);

            $rooms_by_days = $pm_courses_during_absense_days
            ->merge($pm_sub_courses_during_absense_days)
            ->merge($am_courses_during_absense_days)
            ->merge($am_sub_courses_during_absense_days)
            ->values();
            //dd($rooms_by_days);


            //i think they should all be the same format
           //dd($pm_subs_during_absense_days, $pm_sub_courses_during_absense_days,  $pm_courses_during_absense_days, $am_sub_courses_during_absense_days, $available_instructors_by_date_slot, $rooms_by_days);

        //for a all instructors in that list, if they are teaching course in any of the rooms by day during those dates/times missed
        //that means check the rooms by day on those days, and check the crn for the timeslot, if the instructor for the
        //crn in the timeslot matches the instructor, take the instructor out of the list and continue.

        //for each potential available substitution
        foreach ($available_instructors_by_date_slot as $index => $instructor) {
            //look at all the rooms for those days to be subsitututed, and see if the available instructor is teaching
            foreach ($rooms_by_days as $room) {
              //if this room by days matches a day that will be missed, but the course offering entry does't apply to the timeslot, then its irrelevant, so skip it
              //if(($cdate[$room->cdate] == 'AM' && $room->crn != $room->am_crn) || ($cdate[$room->cdate] == 'PM' && $room->crn != $room->pm_crn)){
              if((in_array("$room->cdate,AM", $cdate) && $room->crn != $room->am_crn) || (in_array("$room->cdate,PM", $cdate) && $room->crn != $room->pm_crn)){
                continue;
              }else{
                  //if the entry is relevent to the date and time, and the instructor is teaching during this time, then remove the instructor from the list of teachers who could replace.
                  if($room->instructor_id == $instructor->instructor_id || $room->ta_id == $instructor->instructor_id){
                      $available_instructors_by_date_slot->forget($index);
                  }
              }
          }
        }
        //dd($available_instructors_by_date_slot);
        return collect($available_instructors_by_date_slot)->values()->toArray();
      }


      public function getAvaliableReplacements(Request $req){
        $cdate = $req->cdate;
        $instructor_type = $req->instructor_type;
        $course_id = $req->course_id;
        $instructor_id = $req->instructor_id;

        $available_instructors = $this->queryInstructorsbyAvailibility($instructor_id, $cdate, $instructor_type, $course_id);
        return response()->json(array("substitutions" => $available_instructors), 200);
      }

      /*Used in the substitution use case. AJAX REQUEST*/
      public function GetCoursesInRange(Request $req){
        //something
        $start_date = $req->start_date;
        $end_date  = $req->end_date;
        $instructor_id = $req->instructor_id;

        $errors = collect();
        $matches = Substitution::where('instructor_id', '=', $instructor_id)
            ->whereBetween('start_date', [$start_date, $end_date])
            ->orWhereBetween('end_date', [$start_date,$end_date])
            ->where('instructor_id', '=', $instructor_id)
            ->get();
        //dd($matches);
        if($matches->isNotEmpty()){
          $errors->push("A substitution has already been made for this instructor within this date range.");
          return response()->json(array("error" => $errors->toArray()), 200);
        }

        //dd($start_date);
        //array of course offerings
        $offerings_by_instructor_by_date_range = $this->queryOfferingsByInstructorByDateRange($start_date,$end_date, $instructor_id);
        $offerings_by_instructor_by_date_range->sortBy('crn');
        //dd($offerings_by_instructor_by_date_range);
          /*Massage the data to get rid of unnecessary results*/

          $first = true;
          $count= -1;
          $unique_offerings = collect();
          foreach ($offerings_by_instructor_by_date_range as $elem)
          {
          //  dd($elem);
              /*If the person in question is an instructor, we don't need to know the ta. And vice versa*/
            if($elem->ta_id == $instructor_id){
              unset($elem->instructor_id);
            }else if($elem->instructor_id == $instructor_id){
              unset($elem->ta_id);
            }else{
              dd('error');
            }

            //dd($elem);
            /*separates out unique course offerings, adds a collection of dates to the data*/
            //TODO: should also add keyval to date collection element
            //so do the same thing as above except determine whether the element's dates are am or pm
            //cdate is now a collection that contains the date as the key, and timeslot am/or pm as the value.
            $time_slot = null;
            if($elem->crn == $elem->am_crn){
              $time_slot = 'AM';
            }else{
              $time_slot = 'PM';
            }
            unset($elem->am_crn); unset($elem->pm_crn);
            unset($elem->am_sub); unset($elem->pm_sub);

            if(!$unique_offerings->contains('crn',$elem->crn)){
              $count++;
              $unique_offerings->push($elem);
              $unique_offerings->get($count)->cdate = collect(["$elem->cdate,$time_slot"]);
            }else{
              $unique_offerings->get($count)->cdate->push("$elem->cdate,$time_slot");
            }
            //$first = false;
        }//end of processing all the courses
        //dd($unique_offerings);

        return response()->json(array("offerings" => $unique_offerings), 200);
      }

      public function __construct()
      {
          $this->middleware('auth');
      }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function index()
    {
        //
        $instructors = DB::table('instructors')->get();
        $substitutions = DB::table('substitutions AS s')
        ->join('instructors as i_o', 's.instructor_id', '=', 'i_o.instructor_id')
        ->join('instructors as i_s', function($join){
          $join->on('s.sub_instructor_id', '=', 'i_s.instructor_id')->orOn('s.sub_ta_id', '=', 'i_s.instructor_id');
        })
        ->join('course_offerings as co', 's.sub_crn', '=', 'co.crn')
        ->select('s.instructor_id as original_instructor_id', 's.*', 'i_s.first_name AS substitute_instructor', 'i_o.first_name AS original_instructor', 'co.*')
        ->get();
        //dd($substitutions);

        //dd($instructors);
        return view('pages.substitution', compact('instructors', 'substitutions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $errors = collect();

        //dd($request->substitutions);
        //an array of substitutions for individual course offerings
        $substitutions = collect($request->substitutions);
        DB::beginTransaction();
        foreach ($substitutions as $sub) {
          //dd($sub);
          //first create a row in the substitutions table
            $sub_entry = new Substitution;
            //dd($sub_entry);
            //dd($sub['start_date']);
            $sub_entry->start_date = $sub['start_date'];
            $sub_entry->end_date = $sub['end_date'];
            $sub_entry->sub_crn = $sub['crn'];

            //the request won't have a ta_id if an instructor is being substituted and vice versa
            if(isset($sub['instructor_id'])){
              $sub_entry->instructor_id = $sub['instructor_id'];
              $sub_entry->sub_instructor_id = $sub['sub_id'];
              $original_instructor = $sub['instructor_id'];
            }else if(isset($sub['ta_id'])){
              $sub_entry->instructor_id = $sub['ta_id'];
              $sub_entry->sub_ta_id = $sub['sub_id'];
              $original_instructor = $sub['ta_id'];
            }else {
              $errors->push("no ta or instructor id");
              break;
            }

            $sub_entry->save();
            foreach ($sub['cdate'] as $dateslot) {
              list($date, $slot) = explode(",", $dateslot);
              if ($slot == 'PM') {
                $column = 'pm_sub';
              } else if ($slot == 'AM') {
                $column = 'am_sub';
              }else{
                $errors->push("time slot error while updating RoomsByDay");
                break;
              }
              RoomsByDay::where([
                ['room_id', '=', $sub['room_id']],
                ['cdate', '=', $date],
              ])->update([$column => $sub_entry->id]);
            }//end of sub dates foreach
        }//end of substitutions foreach
        if($errors->isNotEmpty()){
          DB::rollback();
          return response()->json(array("error" => $errors->toArray()), 200);
        }else{
          DB::commit();
          return response()->json(array("success" => ["SUCCESS"]), 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Substitution::destroy($id);
    }
}
