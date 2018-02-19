<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    public $timestamps = false;

    protected $fillable = ['term_start_date', 'intake_id', 'term_no', 'duration_weeks', 'course_weeks', 'break_weeks', 'exam_weeks', 'holidays'];
}
