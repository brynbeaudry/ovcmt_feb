<?php

namespace App\Http\Controllers;

use App\Course;
use App\Http\Requests;
use App\InstructAvail;
use App\Instructor;
use App\CourseInstructor;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
//TODO create a null instructor to "teach" courses where there are no instructors
class InstructorController extends Controller
{
    public function store(Request $req)
    {
        if (isset($req->first_name) && isset($req->email)) {
            //Save Instructor
            $instructor = new Instructor;
            $instructor->first_name = $req->first_name;
            $instructor->email = $req->email;
            try {
                $instructor->save();
            } catch (QueryException $e) {
                return redirect()->back()->with('duplicate_instructor_email', 'Email: ' . $req->email . ' already in use - instructor not added.');
            }

            //Save InstructAvail
            $latestInstructorId = $this->getLastInsertedInstructorId()->instructor_id;
            $instructAvail = new InstructAvail;
            $instructAvail->instructor_id = $latestInstructorId;
            $instructAvail->date_start = $req->date_start;
            $instructAvail->mon_am = isset($req->mon_am) ? 1 : 0;
            $instructAvail->tues_am = isset($req->tues_am) ? 1 : 0;
            $instructAvail->wed_am = isset($req->wed_am) ? 1 : 0;
            $instructAvail->thurs_am = isset($req->thurs_am) ? 1 : 0;
            $instructAvail->fri_am = isset($req->fri_am) ? 1 : 0;
            $instructAvail->mon_pm = isset($req->mon_pm) ? 1 : 0;
            $instructAvail->tues_pm = isset($req->tues_pm) ? 1 : 0;
            $instructAvail->wed_pm = isset($req->wed_pm) ? 1 : 0;
            $instructAvail->thurs_pm = isset($req->thurs_pm) ? 1 : 0;
            $instructAvail->fri_pm = isset($req->fri_pm) ? 1 : 0;
            $instructAvail->save();
        }
        return redirect()->action('InstructorController@index');
    }

    public function assign(Request $req) {
        //Assign course to instructor
        $courseinstructor = CourseInstructor::firstOrNew(['instructor_id' => $req->course_instructor_id,
            'course_id' => $req->course_id, 'intake_no' => $req->intake_no, 'instructor_type' => $req->instructor_type]);
        try {
            $courseinstructor->save();
        } catch (QueryException $e) {
            return redirect()->back()->with("duplicate_course_instructor", "This instructor already has this course assigned as teachable");
        }

        return redirect()->action('InstructorController@index');
    }

    public function delete(Request $req) {
        if (isset($req->instructor_id) && isset($req->course_id)) {
            $courseinstructor = CourseInstructor::where('instructor_id', $req->instructor_id)
                ->where('course_id', $req->course_id)
                ->first();
            $courseinstructor->delete();
            return redirect()->action('InstructorController@index');
        }
    }

    public function deleteInstructor(Request $req) {
    if (Instructor::find($req->modal_instructorid_delete)) {
        $instructor = Instructor::find($req->modal_instructorid_delete);
        $instructor->delete();
    }
    return redirect()->action('InstructorController@index');
}




    public function getLastInsertedInstructorId() {
        return DB::table('instructors')->select('instructor_id')->orderBy('instructor_id', 'DESC')->first();
    }

    public function getAvailabilityFromCheckboxes($req) {
        $checkboxes = $req->instructAvail;

        $availability = array_fill(0,10,0);
        foreach($checkboxes as $avail) {
            $availability[$avail] = 1;
        }
        return $availability;
    }

    public function setInstructorAvailability($instructAvail, $availability) {
        $instructAvail->mon_am = $availability[0];
        $instructAvail->tues_am = $availability[1];
        $instructAvail->wed_am = $availability[2];
        $instructAvail->thurs_am = $availability[3];
        $instructAvail->fri_am = $availability[4];
        $instructAvail->mon_pm = $availability[5];
        $instructAvail->tues_pm = $availability[6];
        $instructAvail->wed_pm = $availability[7];
        $instructAvail->thurs_pm = $availability[8];
        $instructAvail->fri_pm = $availability[9];
    }



    public function listInstructors() {
        return DB::table('instructors as i')
            ->join('instruct_avails as ia', 'i.instructor_id', '=', 'ia.instructor_id')
            ->select('i.instructor_id', 'i.first_name', 'ia.*')
            ->get();
    }

    public function edit(Request $req) {
        $instructor = Instructor::where('instructor_id', $req->modal_instructor_id)->first();
        $instructor->first_name = $req->modal_instructor_name;
        $instructor->save();
        DB::table('instruct_avails')
            ->where('instructor_id', $req->modal_instructor_id)
            ->where('date_start', $req->modal_instruct_avail_start_date)
            ->update(['mon_am' => isset($req->modal_mon_am)? 1 : 0,
                      'tues_am' => isset($req->modal_tues_am)? 1 : 0,
                      'wed_am' => isset($req->modal_wed_am)? 1 : 0,
                      'thurs_am' => isset($req->modal_thurs_am)? 1 : 0,
                      'fri_am' => isset($req->modal_fri_am)? 1 : 0,
                      'mon_pm' => isset($req->modal_mon_pm)? 1 : 0,
                      'tues_pm' => isset($req->modal_tues_pm)? 1 : 0,
                      'wed_pm' => isset($req->modal_wed_pm)? 1 : 0,
                      'thurs_pm' => isset($req->modal_thurs_pm)? 1 : 0,
                      'fri_pm' => isset($req->modal_fri_pm)? 1 : 0]);
        return redirect()->action('InstructorController@index');
    }

    public function deleteCourseInstructor(Request $req) {
        if(isset($req->instructor_id) && isset($req->course_id)) {
            $courseinstructor = CourseInstructor::where('instructor_id', $req->instructor_id)
                ->where('course_id', $req->course_id);
            if(sizeof($courseinstructor) == 1) {
                DB::table('course_instructors')
                    ->where('course_id', $req->course_id)
                    ->where('instructor_id', $req->instructor_id)
                    ->delete();
            }
        }
        return redirect()->action('InstructorController@index');
    }

    public function index() {
        $instructors = $this->listInstructors();
        $courses = Course::all();
        $courseInstructors = CourseInstructor::all();
        return view('pages.manageInstructor', compact('instructors', 'courses', 'courseInstructors'));
    }


}
