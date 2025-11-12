<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{

    protected $fillable = [
        'contracted_treatments_id',
        'schedule',
        'status',
        'session_number',
        'staff_user_id',
        'review',
        'review_score',
    ];

}
