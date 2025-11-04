<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'informed_consent'
    ];

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

    public function ownerProfile()
    {
        return $this->hasOne(OwnerProfile::class);
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

}
