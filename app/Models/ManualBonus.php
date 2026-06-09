<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_settlement_id',
        'amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(PayrollSettlement::class, 'payroll_settlement_id');
    }
}
