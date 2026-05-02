<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ScopesByBranch
{
    protected static function bootScopesByBranch()
    {
        static::addGlobalScope('scopesByBranch', function (Builder $builder) {
            if (auth()->hasUser()) {
                $user = auth()->user();
                
                if ($user->hasRole(['SUPER_ADMIN', 'OWNER', 'PATIENT'])) {
                    return;
                }

                $branchIds = $user->adminsBranches()->pluck('branches.id')->toArray();

                $table = $builder->getModel()->getTable();

                if ($table === 'users') {
                    $builder->where(function ($query) use ($branchIds) {
                        $query->whereHas('patientsBranches', function ($sub) use ($branchIds) {
                            $sub->whereIn('branches.id', $branchIds);
                        })->orWhereDoesntHave('roles', function ($sub) {
                            $sub->where('name', 'PATIENT');
                        });
                    });
                } elseif ($table === 'appointments') {
                    $builder->whereHas('contractedTreatment', function ($sub) use ($branchIds) {
                        $sub->whereIn('branch_id', $branchIds);
                    });
                } elseif (in_array($table, ['assets', 'treatment_orders', 'patient_profiles', 'contracted_treatments', 'orders', 'accounting_records'])) {
                    $builder->whereIn('branch_id', $branchIds);
                }
            }
        });
    }
}
