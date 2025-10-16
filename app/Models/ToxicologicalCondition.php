<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToxicologicalCondition extends Model
{
    use SoftDeletes;

    protected $table = 'toxicological_conditions';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;
}
