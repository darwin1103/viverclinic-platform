<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentCondition extends Model
{

    protected $table = 'treatments';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;
}
