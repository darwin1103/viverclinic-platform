<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contracted_treatments_id',
        'schedule',
        'status',
        'session_number',
        'staff_user_id',
        'review',
        'review_score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'schedule' => 'datetime',
    ];

    /**
     * Get the contracted treatment that this appointment belongs to.
     */
    public function contractedTreatment(): BelongsTo
    {
        // El 'contracted_treatments_id' en la tabla 'appointments'
        // enlaza con el 'id' de la tabla 'contracted_treatments'.
        return $this->belongsTo(ContractedTreatment::class, 'contracted_treatments_id');
    }

    /**
     * Get the staff member (user) assigned to this appointment.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }
}
