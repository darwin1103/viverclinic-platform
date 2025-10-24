<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationCondition extends Model
{

    protected $table = 'medications';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;
}
