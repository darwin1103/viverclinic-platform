<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_types';

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;

    protected $fillable = [
        'id',
        'name',
        'status'
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'document_types_users')->withTimestamps();
    }
}
