<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'commission_amount',
        'total',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'commission_amount' => 'decimal:2',
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

    public function manualBonuses(): HasMany
    {
        return $this->hasMany(ManualBonus::class, 'payroll_settlement_id');
    }

    /**
     * Recalculate the total from all components including manual bonuses.
     */
    public function recalculateTotal(): void
    {
        $bonusesTotal = $this->manualBonuses()->sum('amount');

        $this->update([
            'total' => $this->base_salary
                + $this->commission_amount
                + $bonusesTotal,
        ]);
    }
}
