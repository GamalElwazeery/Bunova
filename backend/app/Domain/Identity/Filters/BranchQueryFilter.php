<?php

namespace App\Domain\Identity\Filters;

use App\Support\Http\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class BranchQueryFilter extends QueryFilter
{
    protected array $allowedFilters = [
        'status',
        'city',
        'country',
        'search',
    ];

    protected array $allowedSorts = [
        'name',
        'code',
        'created_at',
    ];

    protected string $defaultSort = 'created_at';
    protected string $defaultSortDirection = 'desc';

    /**
     * Filter by search term across name and code.
     */
    public function filterSearch(Builder $query, string $term): void
    {
        $term = trim($term);
        if ($term !== '') {
            $query->where(function (Builder $q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
            });
        }
    }
}
