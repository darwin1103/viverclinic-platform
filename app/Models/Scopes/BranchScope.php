<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole('SUPER_ADMIN') || $user->hasRole('SuperAdmin')) {
                return;
            }

            if ($user->hasRole('ADMIN') || $user->hasRole('Admin') || $user->hasRole('Administrador')) {
                $builder->where('branch_id', $user->branch_id);
            }
        }
    }
}
