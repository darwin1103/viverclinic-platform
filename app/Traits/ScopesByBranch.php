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

                if ($user->hasRole('ADMIN')) {
                    $branchIds = $user->adminsBranches()->pluck('branches.id')->toArray();
                } elseif ($user->hasRole('EMPLOYEE')) {
                    $branchIds = $user->employeesBranches()->pluck('branches.id')->toArray();
                    // Fallback to staffProfile branch_id if employees_branches pivot is empty
                    if (empty($branchIds) && $user->staffProfile) {
                        $branchIds = [$user->staffProfile->branch_id];
                    }
                } elseif ($user->hasRole('SALES')) {
                    $branchId = $user->salesProfile?->branch_id;
                    $branchIds = $branchId ? [$branchId] : [];
                } else {
                    $branchIds = [];
                }

                $table = $builder->getModel()->getTable();

                if ($table === 'users') {
                    $builder->where(function ($query) use ($branchIds) {
                        $query->whereHas('patientsBranches', function ($sub) use ($branchIds) {
                            $sub->whereIn('branches.id', $branchIds);
                        })->orWhereHas('patientProfile', function ($sub) use ($branchIds) {
                            $sub->whereIn('patient_profiles.branch_id', $branchIds);
                        })->orWhereDoesntHave('roles', function ($sub) {
                            $sub->where('name', 'PATIENT');
                        });
                    });
                } elseif ($table === 'appointments') {
                    $builder->whereHas('contractedTreatment', function ($sub) use ($branchIds) {
                        $sub->whereIn('contracted_treatments.branch_id', $branchIds);
                    });
                } elseif (in_array($table, ['assets', 'treatment_orders', 'patient_profiles', 'contracted_treatments', 'orders', 'accounting_records'])) {
                    $builder->whereIn($table . '.branch_id', $branchIds);
                }
            }
        });
    }
}
