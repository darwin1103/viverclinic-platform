<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollSettlement extends Model
{
    use HasFactory, \App\Traits\BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'user_id',
        'role_type',
        'period_month',
        'period_year',
        'base_salary',
        'referral_commissions',
        'upgrade_commissions',
        'repurchase_commissions',
        'sales_commissions',
        'manual_bonus',
        'manual_bonus_note',
        'total',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'referral_commissions' => 'decimal:2',
        'upgrade_commissions' => 'decimal:2',
        'repurchase_commissions' => 'decimal:2',
        'sales_commissions' => 'decimal:2',
        'manual_bonus' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
