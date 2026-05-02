<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'virtual_wallet_id',
        'amount',
        'type',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function wallet()
    {
        return $this->belongsTo(VirtualWallet::class, 'virtual_wallet_id');
    }
}
