<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GynecoObstetricCondition extends Model
{

    protected $table = 'gyneco_obstetric_conditions';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;
}
