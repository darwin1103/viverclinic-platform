<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'address',
        'phone',
    ];
    
    public function admins()
    {
        return $this->belongsToMany(User::class, 'admins_branches')->withTimestamps();
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'employees_branches')->withTimestamps();
    }

    public function patients()
    {
        return $this->belongsToMany(User::class, 'patients_branches')->withTimestamps();
    }

}
