<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'instructor_id';
    protected $fillable = ['first_name', 'email'];
}
