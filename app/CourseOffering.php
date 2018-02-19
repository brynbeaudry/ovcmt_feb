<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseOffering extends Model
{
    public $timestamps = false;
    protected $table = 'course_offerings';
    protected $fillable = ['term_id', 'course_id', 'instructor_id', 'intake_no', 'ta_id'];
}
