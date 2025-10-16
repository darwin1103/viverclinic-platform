<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PathologicalCondition extends Model
{
    use SoftDeletes;

    protected $table = 'pathological_conditions';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;

    protected $fillable = [
        'id',
        'name',
        'status'
    ];
}
