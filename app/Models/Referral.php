<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_name',
        'referred_email',
        'referred_phone',
        'status',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }
}
