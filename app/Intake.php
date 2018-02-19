<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Intake extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'intake_id';
    protected $fillable = ['start_date', 'intake_no'];
}
