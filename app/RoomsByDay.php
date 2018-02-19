<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomsByDay extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $table = 'rooms_by_days';
    protected $fillable = ['room_id', 'cdate', 'am_crn', 'pm_crn', 'am_sub', 'pm_sub'];
}
