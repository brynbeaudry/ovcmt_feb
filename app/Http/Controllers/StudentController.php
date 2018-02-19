<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index() {
        $students = DB::table('users')
            ->where('usertype', 'student')
            ->get();
        return view('pages.manageStudents', compact('students'));
    }
}
