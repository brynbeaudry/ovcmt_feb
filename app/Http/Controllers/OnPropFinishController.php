<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnPropFinishController extends Controller
{
    public function index()
    {
        if (isset($data['date'])) {
            $dateError = $data['date'];
            return view('pages.propagateschedule', compact('dateError'));
        }
        return view('pages.propagateschedule');
    }
}
