<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreatmentCondition extends Model
{
    use SoftDeletes;

    protected $table = 'treatments';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;
}
