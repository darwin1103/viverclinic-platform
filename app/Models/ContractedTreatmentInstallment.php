<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractedTreatmentInstallment extends Model
{
    protected $fillable = [
        'contracted_treatment_id',
        'installment_number',
        'price',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function contractedTreatment()
    {
        return $this->belongsTo(ContractedTreatment::class);
    }
}
