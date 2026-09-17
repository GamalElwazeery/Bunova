<?php

namespace App\Support\Http;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PaginationParams
{
    public const DEFAULT_PER_PAGE = 15;
    public const MAX_PER_PAGE = 100;

    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
        public readonly ?string $cursor = null,
        public readonly ?string $sortBy = null,
        public readonly string $sortDirection = 'asc'
    ) {}

    /**
     * Create PaginationParams from HTTP request with safe bounds.
     */
    public static function fromRequest(
        Request $request,
        int $defaultPerPage = self::DEFAULT_PER_PAGE,
        int $maxPerPage = self::MAX_PER_PAGE,
        ?string $defaultSort = null,
        string $defaultDirection = 'asc'
    ): self {
        $perPageRaw = $request->query('per_page', $defaultPerPage);
        $perPage = is_numeric($perPageRaw) ? (int) $perPageRaw : $defaultPerPage;
        $perPage = max(1, min($maxPerPage, $perPage));

        $pageRaw = $request->query('page', 1);
        $page = is_numeric($pageRaw) ? max(1, (int) $pageRaw) : 1;

        $cursor = $request->query('cursor');
        if (!is_string($cursor) || trim($cursor) === '') {
            $cursor = null;
        }

        $sortBy = $request->query('sort_by', $defaultSort);
        if (!is_string($sortBy) || trim($sortBy) === '') {
            $sortBy = $defaultSort;
        }

        $sortDirectionRaw = strtolower((string) $request->query('sort_direction', $defaultDirection));
        $sortDirection = in_array($sortDirectionRaw, ['asc', 'desc'], true) ? $sortDirectionRaw : 'asc';

        return new self(
            page: $page,
            perPage: $perPage,
            cursor: $cursor,
            sortBy: $sortBy,
            sortDirection: $sortDirection
        );
    }

    /**
     * Determine if cursor-based pagination is requested.
     */
    public function isCursor(): bool
    {
        return $this->cursor !== null;
    }

    /**
     * Apply pagination to an Eloquent query builder.
     */
    public function paginate(Builder $query): LengthAwarePaginator|CursorPaginator
    {
        if ($this->sortBy !== null) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        if ($this->isCursor()) {
            return $query->cursorPaginate(
                perPage: $this->perPage,
                cursor: $this->cursor
            );
        }

        return $query->paginate(
            perPage: $this->perPage,
            page: $this->page
        );
    }
}
