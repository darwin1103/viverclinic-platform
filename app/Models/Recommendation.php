<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use \App\Traits\ScopesByBranch;

    protected $fillable = [
        'branch_id',
        'title',
        'image',
        'content',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
