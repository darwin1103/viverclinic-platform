<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'active',
        'main_image',
        'sessions',
        'days_between_sessions',
        'terms_conditions',
    ];

    /**
     * The branches that offer this treatment.
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_treatment')
                    ->withPivot('price') // Importante para acceder al precio
                    ->withTimestamps();
    }
}
