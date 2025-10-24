<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToxicologicalCondition extends Model
{

    protected $table = 'toxicological_conditions';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;
}
