<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class Branch extends Model
{

    use SlugTrait;

    protected $usedSlugs = [];

    protected $fillable = [
        'name',
        'address',
        'phone',
        'google_maps_url',
        'logo',
        'slug',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted(): void
    {

        static::creating(function (Branch $post) {
            $post->slug = self::createSlug($post->name, 0, Branch::class);
        });

    }
    
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

    public function packages(): HasMany
    {
        return $this->hasMany(BranchTreatment::class);
    }

    /**
     * Obtiene los tratamientos asociados a la sucursal a través de los paquetes.
     */
    public function treatments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Treatment::class,       // El modelo final al que queremos acceder
            BranchTreatment::class, // El modelo intermedio (nuestro "paquete")
            'branch_id',            // La clave foránea en la tabla intermedia (`branch_treatment`)
            'id',                   // La clave foránea en la tabla final (`treatments`)
            'id',                   // La clave local en este modelo (`branches`)
            'treatment_id'          // La clave local en la tabla intermedia (`branch_treatment`)
        );
    }

}
