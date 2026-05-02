<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Appointment;
use App\Models\Referral;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\ScopesByBranch;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, ScopesByBranch;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'informed_consent',
        'referral_code',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                do {
                    $code = strtoupper(\Illuminate\Support\Str::random(8));
                } while (static::withoutGlobalScopes()->where('referral_code', $code)->exists());
                
                $user->referral_code = $code;
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    public function adminsBranches()
    {
        return $this->belongsToMany(Branch::class, 'admins_branches')->withTimestamps();
    }

    public function employeesBranches()
    {
        return $this->belongsToMany(Branch::class, 'employees_branches')->withTimestamps();
    }

    public function patientsBranches()
    {
        return $this->belongsToMany(Branch::class, 'patients_branches')->withTimestamps();
    }

    public function gender() {
        return $this->belongsTo(Gender::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function pathologicalCondition()
    {
        return $this->belongsTo(PathologicalCondition::class, 'pathological_id');
    }

    public function toxicologicalCondition()
    {
        return $this->belongsTo(ToxicologicalCondition::class, 'toxicological_id');
    }

    public function gynecoObstetricCondition()
    {
        return $this->belongsTo(GynecoObstetricCondition::class, 'gyneco_obstetric_id');
    }

    public function medicationCondition()
    {
        return $this->belongsTo(MedicationCondition::class, 'medication_id');
    }

    public function dietaryCondition()
    {
        return $this->belongsTo(DietaryCondition::class, 'dietary_id');
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatment_id');
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class);
    }

    public function virtualWallet(): HasOne
    {
        return $this->hasOne(VirtualWallet::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'staff_user_id');
    }

    /**
     * Referidos hechos por este usuario.
     */
    public function referralsMade(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Referido recibido (si este usuario fue referido por alguien).
     */
    public function referralReceived(): HasOne
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * Generar un código de referido único.
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = 'VV-' . strtoupper(Str::random(6));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Obtener el link de referido del usuario.
     */
    public function getReferralLink(): ?string
    {
        if (!$this->referral_code) {
            return null;
        }

        $branch = $this->patientProfile?->branch;
        if ($branch) {
            return route('registration-by-branch.create', ['branch' => $branch->slug]) . '?ref=' . $this->referral_code;
        }

        return url('/register') . '?ref=' . $this->referral_code;
    }
}
