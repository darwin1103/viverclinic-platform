<?php

namespace App\Traits;

use App\Models\Scopes\BranchScope;

trait BelongsToBranch
{
    /**
     * Boot the BelongsToBranch trait for a model.
     *
     * @return void
     */
    protected static function bootBelongsToBranch()
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function ($model) {
            if (auth()->check() && empty($model->branch_id)) {
                $user = auth()->user();
                if (isset($user->branch_id)) {
                    $model->branch_id = $user->branch_id;
                }
            }
        });
    }
}
