<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentInstallment extends Model
{
    protected $fillable = [
        'branch_treatment_id',
        'installment_number',
        'price'
    ];

    public function package()
    {
        return $this->belongsTo(BranchTreatment::class, 'branch_treatment_id');
    }
}
