<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractedTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'treatment_id',
        'contracted_packages',
        'contracted_additionals',
        'selected_zones',
        'total_price',
        'status',
        'sessions',
        'days_between_sessions',
        'terms_acepted',
        'is_pregnant',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Automatically encode/decode these JSON columns to/from arrays
        'contracted_packages' => 'array',
        'contracted_additionals' => 'array',
        'selected_zones' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}
