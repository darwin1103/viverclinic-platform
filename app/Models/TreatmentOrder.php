<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentOrder extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'contracted_treatment_id',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'bank_name',
        'payment_source_id',
        'amount_in_cents',
        'currency',
        'customer_email',
        'acceptance_token',
        'is_juridical',
        'document_type',
        'document_number',
        'financial_institution_code',
        'payment_description',
        'payment_receipt',
        'paid_installments_ids',
    ];

    protected $casts = [
        'paid_installments_ids' => 'array',
    ];
}
