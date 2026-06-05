<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepurchaseCommission extends Model
{
    use HasFactory, \App\Traits\BelongsToBranch;

    protected $fillable = [
        'contracted_treatment_id',
        'branch_id',
        'staff_user_id',
        'commission_amount',
        'commission_type',
        'commission_value',
        'treatment_total',
        'status',
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'treatment_total' => 'decimal:2',
    ];

    public function contractedTreatment(): BelongsTo
    {
        return $this->belongsTo(ContractedTreatment::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }
}
