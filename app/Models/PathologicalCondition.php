<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PathologicalCondition extends Model
{

    protected $table = 'pathological_conditions';

    protected $fillable = [
        'id',
        'name',
        'status'
    ];
}
