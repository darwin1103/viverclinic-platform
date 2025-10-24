<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietaryCondition extends Model
{

    protected $table = 'dietary_conditions';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;
}
