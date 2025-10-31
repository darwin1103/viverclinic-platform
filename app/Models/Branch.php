<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'google_maps_url',
        'logo',
    ];
    
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admins_branches')->withTimestamps();
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'employees_branches')->withTimestamps();
    }

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'patients_branches')->withTimestamps();
    }

    /**
     * The treatments available at this branch.
     */
    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(Treatment::class, 'branch_treatment')
                    ->withPivot('price') // Importante para acceder al precio
                    ->withTimestamps();
    }

}
