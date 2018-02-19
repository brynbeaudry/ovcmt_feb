<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstructAvail extends Model
{
    public $timestamps = false;
    protected $fillable = ['instructor_id', 'date_start',
                            'mon_am', 'mon_pm',
                            'tues_am', 'tues_pm',
                            'wed_am', 'wed_pm',
                            'thurs_am', 'thurs_pm',
                            'fri_am', 'fri_pm'];
}
