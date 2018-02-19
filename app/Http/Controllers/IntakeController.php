<?php

namespace App\Http\Controllers;
use App\Intake;
use DateTime;
use Illuminate\Http\Request;
use App\Term;
use DB;
class IntakeController extends Controller
{
    public function store(Request $req) {
        $intake = new Intake;
        $intake->start_date = $req->start_date;
        $intake_month = DateTime::createFromFormat('Y-m-d', $req->start_date)->format('n');
        if($intake_month == 9) {
            $intake->intake_no = 'A';
        } else {
            $intake->intake_no = 'B';
        }
        //VALIDATION
        if($this->validateIntake($intake)) {
            $intake->save();
            //CREATE TERMS FOR EACH INTAKE
            $this->createTerms($intake);
        }
        //TODO else error message 

        return redirect()->action('IntakeController@index');
    }

    public function updateIntake(Request $req)
    {
        if((DateTime::createFromFormat('Y-m-d', $req->modal_start_date)->format('n') == 9
            && $req->modal_intake_no == 'A') ||
            (DateTime::createFromFormat('Y-m-d', $req->modal_start_date)->format('n') == 1
                && $req->modal_intake_no == 'B')) {
            Intake::where('intake_id', $req->modal_intake_id)
                ->update(['start_date'=>$req->modal_start_date]);
        }
        return redirect()->action('IntakeController@index');
    }

    public function deleteIntake(Request $req) {
        if (Intake::find($req->modal_intakeid_delete)) {
            $intake = Intake::find($req->modal_intakeid_delete);
            $intake->delete();
        }
        return redirect()->action('IntakeController@index');
    }

    /**
     * Creates terms with default values.
     * @param Intake $intake
     */
    public function createTerms(Intake $intake)
    {
        $term_starts = $this->makeTermStarts($intake->start_date);
        for($i = 1; $i<5; $i++) {
            $term = new Term;
            $term->term_start_date = $term_starts["term$i"];
            $term->intake_id = $intake->intake_id;
            $term->term_no = $i;
            if($i === 1 || $i === 3) {
                $term->course_weeks = 13;
                $term->break_weeks = 1;
                $term->exam_weeks = 1;
            } elseif($i === 2) {
                $term->course_weeks = 22;
                $term->exam_weeks = 2;
                if($intake->intake_no === 'A') {
                    $term->break_weeks = 2;
                } else {
                    $term->break_weeks = 3;
                }
            } elseif($i === 4) {
                $term->course_weeks = 30;
                $term->exam_weeks = 2;
                if($intake->intake_no === 'A') {
                    $term->break_weeks = 2;
                } else {
                    $term->break_weeks = 4;
                }
            }
            $term->holidays = 0;
            $term->duration_weeks = $term->course_weeks + $term->exam_weeks + $term->break_weeks;
            $term->save();
        }
    }

    public function makeTermStarts($program_start)
    {
        $term_starts = array();
        $startdate = DateTime::createFromFormat('Y-m-d', $program_start);
        $start_year = idate('Y', $startdate->getTimestamp());
        //INTAKE A
        if($startdate->format('m') == 9) {
            $term_starts['term1'] = $program_start;
            $term_starts['term2'] = $this->makeTermStartDate(++$start_year, "1");
            $term_starts['term3'] = $this->makeTermStartDate($start_year, "9");
            $term_starts['term4'] = $this->makeTermStartDate(++$start_year, "1");
        } else {
            $term_starts['term1'] = $program_start;
            $term_starts['term2'] = $this->makeTermStartDate($start_year, "9");;
            $term_starts['term3'] = $this->makeTermStartDate(++$start_year, "1");;
            $term_starts['term4'] = $this->makeTermStartDate($start_year, "9");;
        }
        return $term_starts;
    }

    public function makeTermStartDate($year, $month)
    {
        return DateTime::createFromFormat('Y-m-d', "$year"."-"."$month"."-01");
    }

    public function validateIntake(Intake $intake)
    {
        $start_month = DateTime::createFromFormat('Y-m-d', $intake->start_date)->format('n');
        $start_year = DateTime::createFromFormat('Y-m-d', $intake->start_date)->format('Y');
        $count = DB::table('intakes')
            ->whereMonth('start_date', $start_month)
            ->where('intake_no', $intake->intake_no)
            ->whereYear('start_date', $start_year)
            ->count();

        return $count === 0 && ($start_month == 1 || $start_month == 9);
    }

    public function index() {
        $intakes = DB::table('intakes AS i')
            ->select('i.*')
            ->orderBy('i.start_date', 'DESC')
            ->get();
        return view('pages.manageIntake', compact('intakes'));
    }
}
