<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareTip extends Model
{
    use HasFactory, \App\Traits\ScopesByBranch;

    protected $fillable = [
        'branch_id',
        'title',
        'description',
        'image',
        'content',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
