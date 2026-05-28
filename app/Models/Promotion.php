<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $fillable = [
        'title',
        'description',
        'discount',
        'start_date',
        'end_date',
        'is_active',
        'treatment_id',
        'branch_treatment_id',
        'branch_id',
        'discount_type',
        'activation_mode',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'discount' => 'decimal:2',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(BranchTreatment::class, 'branch_treatment_id');
    }

    public function getIsCurrentlyActiveAttribute(): bool
    {
        if ($this->activation_mode === 'manual') {
            return $this->is_active;
        }

        $today = now()->startOfDay();

        if ($this->start_date && $today->lt($this->start_date->startOfDay())) {
            return false;
        }

        if ($this->end_date && $today->gt($this->end_date->endOfDay())) {
            return false;
        }

        return true;
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return ((float) $this->discount) . '%';
        }
        return '$' . number_format($this->discount, 0, ',', '.') . ' COP';
    }
}
