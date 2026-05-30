<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchTreatment extends Model
{
    use HasFactory;

    // Especifica la tabla si el nombre no sigue la convención
    protected $table = 'branch_treatment';

    protected $fillable = [
        'branch_id',
        'treatment_id',
        'name',
        'price',
        'big_zones',
        'mini_zones',
        'allow_installments',
        'installment_conditions',
        'custom_sessions',
        'sessions',
    ];

    protected $casts = [
        'allow_installments' => 'boolean',
        'custom_sessions' => 'boolean',
        'sessions' => 'integer',
    ];

    // Relación opcional para acceder al tratamiento desde un paquete
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    // Relación opcional para acceder a la sucursal desde un paquete
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function installments()
    {
        return $this->hasMany(TreatmentInstallment::class);
    }
}
