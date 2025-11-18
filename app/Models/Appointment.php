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
        'contracted_treatment_id',
        'schedule',
        'attended',
        'status',
        'session_number',
        'staff_user_id',
        'review',
        'review_score',
        'notification_reminder_sent',
        'uses_of_hair_removal_shots',
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
        return $this->belongsTo(ContractedTreatment::class);
    }

    /**
     * Get the staff member (user) assigned to this appointment.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }
}
