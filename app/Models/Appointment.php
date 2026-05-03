<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\ScopesByBranch;
use App\Models\Treatment;
use App\Models\Setting;

class Appointment extends Model
{
    use HasFactory, ScopesByBranch;

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

    /**
     * Get the dynamic max shots limit for this appointment based on contracted treatment zones.
     */
    public function getMaxShotsLimitAttribute(): int
    {
        $contract = $this->contractedTreatment;
        if (!$contract) return 0;

        $selectedZones = $contract->selected_zones ?? [];

        $bigZones = Treatment::$bigZones;
        $smallZones = Treatment::$smallZones;
        $miniZones = Treatment::$miniZones;

        $zoneCount = 0;
        $minizoneCount = 0;

        foreach ($selectedZones as $zone) {
            if (in_array($zone, $bigZones) || in_array($zone, $smallZones)) {
                $zoneCount++;
            } elseif (in_array($zone, $miniZones)) {
                $minizoneCount++;
            }
        }

        // If no zones were selected, we assume at least 1 base zone for the treatment
        if ($zoneCount == 0 && $minizoneCount == 0) {
            $zoneCount = 1;
        }

        $shotsPerZone = (int) Setting::get('shots_per_zone', 600);
        $shotsPerMinizone = (int) Setting::get('shots_per_minizone', 200);

        return ($zoneCount * $shotsPerZone) + ($minizoneCount * $shotsPerMinizone);
    }
}
