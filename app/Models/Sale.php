<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;

class Sale extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'staff_user_id',
        'patient_user_id',
        'contracted_treatment_id',
        'type',
        'first_payment_amount',
        'notes',
    ];

    protected $casts = [
        'first_payment_amount' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    public function contractedTreatment()
    {
        return $this->belongsTo(ContractedTreatment::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
