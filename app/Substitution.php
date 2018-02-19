<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Substitution extends Model
{
    //
    public $incrementing = true;
    public $timestamps = false;
    protected $table = 'substitutions';
    protected $fillable = ['start_date', 'end_date', 'sub_crn', 'sub_instructor_id','sub_ta_id'];
}
