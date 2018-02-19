<?php

namespace App\Http\Controllers;

use App\CourseInstructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Instructor;
use App\CourseOffering;
use App\Term;

class AssignController extends Controller
{
    public function assignCourse(Request $req) {
        $intake_no = DB::table('terms AS t')
            ->join('intakes AS i', 't.intake_id', '=', 'i.intake_id')
            ->where('t.term_id', $req->term_id)
            ->pluck('intake_no')
            ->first();
        if($req->ta_id != "none" || $req->instructor_id != "none") {
            $courseoffering = CourseOffering::firstOrNew(['term_id' => $req->term_id, 'course_id' => $req->course_id, 'intake_no' => $intake_no]);
        } else {
            return redirect()->back()->with('error', $req->course_id);

        }

        if($req->ta_id != "none") {
            $courseoffering->ta_id = $req->ta_id;
        }
        if ($req->instructor_id != "none") {
            $courseoffering->instructor_id = $req->instructor_id;
        }
        $courseoffering->save();
        return redirect()->action('AssignController@index');
    }
    
    public function unassignCourse(Request $req) {
        $courseoffering = CourseOffering::where('course_id', $req->course_id)
            ->where('term_id', $req->term_id)
            ->where('intake_no', $req->intake_no);
        if (isset($req->instructor_id)) {
            $courseoffering->where('instructor_id', $req->instructor_id);
        }
        if (isset($req->ta_id)) {
            $courseoffering->where('instructor_id', $req->instructor_id);
        }
        $courseoffering->delete();
        return redirect()->action('AssignController@index');
    }

    // added reassign course function
    public function reassignCourse(Request $req) {
        $courseoffering = DB::table('course_offerings')
            ->where('course_id', $req->course_id_reassign)
            ->where('term_id', $req->term_id_reassign)
            ->where('intake_no', $req->intake_no_reassign);

        // returns how many columns are being updated
        $reassignInstructor = $courseoffering->update(['instructor_id' => $req->instructor_id_reassign]);

        if ($reassignInstructor != 0) {
            // checks if TA is being changed
            if ($req->ta_id_reassign == $req->ta_id_original) {
                return redirect()->action('AssignController@index')->with('message', 'Successfully updated Instructor.');
            } else {
                $reassignTA = $courseoffering->update(['ta_id' => $req->ta_id_reassign]);
                return redirect()->action('AssignController@index')->with('message', 'Successfully updated both Instructor and TA.');
            }
        } else {
            // checks if TA is being changed
            if ($req->ta_id_reassign == $req->ta_id_original) {
                return redirect()->action('AssignController@index')->with('failuremessage', 'Failed to reassign instructor; no changes were made');
            } else {
                $reassignTA = $courseoffering->update(['ta_id' => $req->ta_id_reassign]);
                return redirect()->action('AssignController@index')->with('message', 'Successfully updated TA.');
            }
        }
    }   

    public function getTerms() {
        $terms = DB::table('terms AS t')
            ->join('intakes AS i', 't.intake_id', '=', 'i.intake_id')
            ->select('t.term_id AS term_id',
                't.term_start_date AS term_start_date',
                't.intake_id AS intake_id',
                't.term_no AS term_no',
                'i.intake_no AS intake_no',
                'i.start_date AS program_start_date')
            ->orderBy('t.term_start_date', 'asc')
            ->orderBy('t.term_no', 'asc', 'i.intake_no')
            ->get();
        return $terms;
    }
    public function index() {
        $terms = $this->getTerms();
        return view('pages.assign', compact('terms'));
    }
}
