<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Apply filters to the query based on the request.
     *
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    public function applyFilters(Request $request, Builder $query): Builder
    {
        // Filter by search term (name or email)
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        return $query;
    }
}
