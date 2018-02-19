<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarDate extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'calendar_dates';

    protected $fillable = ['cdate', 'cdayOfMonth', 'cmonth', 'cyear', 'cdayOfWeek', 'cweek', 'isWeekday', 'isHoliday', 'holidayDesc'];
}
