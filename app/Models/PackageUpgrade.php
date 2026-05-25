<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageUpgrade extends Model
{
    use HasFactory, \App\Traits\BelongsToBranch;

    protected $fillable = [
        'contracted_treatment_id',
        'branch_id',
        'old_package_data',
        'new_package_id',
        'new_package_data',
        'price_difference',
        'staff_user_id',
        'commission_amount',
        'commission_type',
        'commission_value',
        'payment_method',
        'payment_status',
        'old_selected_zones',
        'new_selected_zones',
        'processed_by',
    ];

    protected $casts = [
        'old_package_data' => 'array',
        'new_package_data' => 'array',
        'old_selected_zones' => 'array',
        'new_selected_zones' => 'array',
        'price_difference' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
    ];

    public function contractedTreatment(): BelongsTo
    {
        return $this->belongsTo(ContractedTreatment::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function newPackage(): BelongsTo
    {
        return $this->belongsTo(BranchTreatment::class, 'new_package_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
