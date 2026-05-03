<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesProfile extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'commission_divisor',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
