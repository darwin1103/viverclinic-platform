<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 'generous';

    protected $fillable = [
        'id',
        'name',
        'code',
        'status'
    ];
}
