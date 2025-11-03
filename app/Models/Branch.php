<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
