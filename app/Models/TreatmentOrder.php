<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentOrder extends Model
{
    use \App\Traits\ScopesByBranch;

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

    // --- RELACIONES ---

    // ESTA ES LA QUE FALTA Y CAUSA EL ERROR
    public function contractedTreatment(): BelongsTo
    {
        return $this->belongsTo(ContractedTreatment::class, 'contracted_treatment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
