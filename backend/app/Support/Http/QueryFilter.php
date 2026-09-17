<?php

namespace App\Support\Http;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    /**
     * Allowed filter keys.
     *
     * @var array<string>
     */
    protected array $allowedFilters = [];

    /**
     * Allowed sortable column names.
     *
     * @var array<string>
     */
    protected array $allowedSorts = [];

    /**
     * Default sort column.
     */
    protected string $defaultSort = 'created_at';

    /**
     * Default sort direction.
     */
    protected string $defaultSortDirection = 'desc';

    public function __construct(
        protected Request $request
    ) {}

    /**
     * Apply configured filters, sorts, and search to query builder.
     */
    public function apply(Builder $query): Builder
    {
        $this->applyFilters($query);
        $this->applySorting($query);

        return $query;
    }

    /**
     * Apply whitelist filters from request query parameters.
     */
    protected function applyFilters(Builder $query): void
    {
        foreach ($this->request->query() as $key => $value) {
            if ($value === null || $value === '' || !in_array($key, $this->allowedFilters, true)) {
                continue;
            }

            $methodName = 'filter'.Str::studly($key);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($query, $value);
            } else {
                // Default equality comparison for simple column filters
                $query->where($key, $value);
            }
        }
    }

    /**
     * Apply validated sorting to query builder.
     */
    protected function applySorting(Builder $query): void
    {
        $sortBy = $this->request->query('sort_by', $this->defaultSort);
        $direction = strtolower((string) $this->request->query('sort_direction', $this->defaultSortDirection));

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = $this->defaultSortDirection;
        }

        if (in_array($sortBy, $this->allowedSorts, true)) {
            $query->orderBy($sortBy, $direction);
        } elseif ($this->defaultSort !== null && in_array($this->defaultSort, $this->allowedSorts, true)) {
            $query->orderBy($this->defaultSort, $this->defaultSortDirection);
        }
    }
}
