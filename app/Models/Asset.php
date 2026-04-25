<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ScopesByBranch;

class Asset extends Model
{
    use ScopesByBranch;

    protected $fillable = ['name', 'stock', 'branch_id'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(AssetNote::class)->latest();
    }
}
