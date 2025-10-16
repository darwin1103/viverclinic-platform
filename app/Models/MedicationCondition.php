<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationCondition extends Model
{
    use SoftDeletes;

    protected $table = 'medications';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;
}
