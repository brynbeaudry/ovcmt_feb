<?php

namespace App\Http\Controllers;
use DB;
use App\Term;
use App\Intake;
use Illuminate\Http\Request;
use DateTime;
class TermController extends Controller
{
    public function store(Request $req) {
        // TODO: add logic for determining other columns
        Term::where('term_id', $req->modal_term_id)
            ->update(['term_start_date'=>$req->modal_term_start_date,
                      'course_weeks'=>$req->modal_course_weeks,
                      'break_weeks'=>$req->modal_break_weeks,
                      'exam_weeks'=>$req->modal_exam_weeks,
                      'duration_weeks'=>$req->modal_exam_weeks+$req->modal_break_weeks+$req->modal_course_weeks]);
        $terms = DB::table('terms AS t')
            ->join('intakes AS i','t.intake_id', '=', 'i.intake_id')
            ->select('t.*', 'i.intake_no')
            ->get();

        $intakes = Intake::orderBy('start_date', 'DESC')->get();
        return view('pages.manageTerm', compact('intakes', 'terms'));
    }

    public function searchTerm(Request $req)
    {
        $terms = DB::table('terms AS t')
            ->join('intakes AS i','t.intake_id', '=', 'i.intake_id')
            ->select('t.*', 'i.intake_no')
            ->where('i.intake_id', $req->choose_intake)
            ->get();
        $intakes = Intake::orderBy('start_date', 'DESC')->get();
        return view('pages.manageTerm', compact('intakes', 'terms'));
    }

    public function makeTermStarts($program_start, $term_no)
    {
        $startdate = DateTime::createFromFormat('Y-m-d', $program_start);
        $start_year = idate('Y', $startdate->getTimestamp());
        //INTAKE A
        if($startdate->format('m') === 9) {
            switch($term_no) {
                case 1:
                    return $program_start;
                    break;
                case 2:
                    return $this->makeTermStartDate($start_year++, "1");
                    break;
                case 3:
                    return $this->makeTermStartDate($start_year, "9");
                    break;
                case 4:
                    return $this->makeTermStartDate($start_year++, "1");
                    break;
            }
        } else {
            switch($term_no) {
                case 1:
                    return $program_start;
                    break;
                case 2:
                    return $this->makeTermStartDate($start_year, "9");
                    break;
                case 3:
                    $this->makeTermStartDate($start_year++, "1");
                    break;
                case 4:
                    $this->makeTermStartDate($start_year, "9");
                    break;
            }
        }
        return $program_start;
    }

    public function deleteTerm(Request $req) {
        if (Intake::find($req->modal_termId_delete)) {
            $intake = Term::find($req->modal_termId_delete);
            $intake->delete();
        }
        return redirect()->action('TermController@index');
    }

    public function makeTermStartDate($year, $month)
    {
        return DateTime::createFromFormat('Y-m-d', "$year-$month-01");
    }

    public function index() {
        $terms =  DB::table('terms AS t')
            ->join('intakes AS i','t.intake_id', '=', 'i.intake_id')
            ->select('t.*', 'i.intake_no')
            ->get();
        $intakes = Intake::orderBy('start_date', 'DESC')->get();
        return view('pages.manageTerm', compact('intakes', 'terms'));
    }
}
