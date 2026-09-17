<?php

namespace App\Http\Controllers\Api;

use App\Domain\Identity\Filters\BranchQueryFilter;
use App\Domain\Identity\Models\Branch;
use App\Http\Controllers\Controller;
use App\Support\Http\ApiResponse;
use App\Support\Http\PaginationParams;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchController extends Controller
{
    /**
     * List branches with filtering, sorting, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Branch::query();

        // Apply filters & sorting
        $filter = new BranchQueryFilter($request);
        $filter->apply($query);

        // Apply page or cursor pagination
        $pagination = PaginationParams::fromRequest(
            $request,
            defaultPerPage: 15,
            maxPerPage: 100,
            defaultSort: 'created_at',
            defaultDirection: 'desc'
        );

        $results = $pagination->paginate($query);

        return ApiResponse::paginated($results, 'Branches retrieved successfully.');
    }

    /**
     * Create a branch (idempotent mutating endpoint).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'uuid'],
            'brand_id' => ['required', 'uuid'],
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:2'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $branch = Branch::create($validated);

        return ApiResponse::success(
            data: $branch,
            message: 'Branch created successfully.',
            status: Response::HTTP_CREATED
        );
    }

    /**
     * Retrieve single branch.
     */
    public function show(string $id): JsonResponse
    {
        $branch = Branch::findOrFail($id);

        return ApiResponse::success($branch);
    }
}
