<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'status',
        'bonus_sessions',
        'staff_id',
        'staff_commission',
        'staff_commission_status',
        'rewarded_at',
    ];

    protected $casts = [
        'rewarded_at' => 'datetime',
        'staff_commission' => 'decimal:2',
    ];

    /**
     * Usuario que hizo la referencia.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Usuario referido (nuevo).
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Empleada que recibe la comisión.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
