<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseInstructor extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = ['instructor_id', 'course_id',  'intake_no', 'instructor_type'];
}
