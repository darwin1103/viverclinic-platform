<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ScopesByBranch;

class GlobalSchedule extends Model
{
    use HasFactory, ScopesByBranch;

    protected $fillable = [
        'branch_id',
        'day_of_week',
        'start_time',
        'end_time',
        'regular_slots',
        'sales_slots',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
