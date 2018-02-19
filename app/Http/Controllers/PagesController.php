<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{


    public function home()
    {
        return view('welcome');
    }

    public function about()
    {
        return view('pages.about');
    }


    public function adminauth()
	{
        return view('pages.adminauth');
    }
    public function staffauth()
	{
        return view('pages.staffauth');
    }
    public function studauth()
	{
        return view('pages.studauth');
    }

    public function masterscheduleview()
	{
        return view('pages.masterscheduleview');
    }
    public function addschedule()
	{
        return view('pages.addschedule');
    }
    public function schedulestudent()
	{
        return view('pages.schedulestudent');
    }
    public function schedulestaff()
	{
        return view('pages.schedulestaff');
    }
    public function addcourse()
	{
        return view('pages.addcourse');
    }
    public function draganddropschedule()
	{
        return view('pages.draganddropschedule');
    }
    public function propagateschedule()
	{
        return view('pages.propagateschedule');
    }
	public function newspage()
	{
		return view('pages.newsPage');
	}
}
