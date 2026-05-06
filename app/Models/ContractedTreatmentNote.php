<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractedTreatmentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'contracted_treatment_id',
        'user_id',
        'content',
    ];

    public function contractedTreatment(): BelongsTo
    {
        return $this->belongsTo(ContractedTreatment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
