<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use App\Domain\Shared\Models\IdempotencyRecord;
use App\Http\Middleware\CorrelationIdMiddleware;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiConventionsAndIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = Organization::create([
            'name' => 'Kite Coffee Group',
            'slug' => 'kite-coffee',
            'commercial_status' => 'active',
        ]);

        $this->brand = Brand::create([
            'organization_id' => $this->org->id,
            'name' => 'Kite Espresso',
            'slug' => 'kite-espresso',
        ]);
    }

    public function test_correlation_id_is_generated_and_returned_when_omitted(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200);
        $response->assertHeader(CorrelationIdMiddleware::HEADER_NAME);
        $response->assertHeader('X-Bunova-Version', 'v1');

        $headerValue = $response->headers->get(CorrelationIdMiddleware::HEADER_NAME);
        $this->assertNotEmpty($headerValue);
        $this->assertTrue(Str::isUuid($headerValue) || strlen($headerValue) >= 16);
    }

    public function test_client_correlation_id_is_preserved_and_propagated(): void
    {
        $customCorrelationId = 'client-trace-abc-12345';

        $response = $this->withHeaders([
            CorrelationIdMiddleware::HEADER_NAME => $customCorrelationId,
        ])->getJson('/api/v1/health');

        $response->assertStatus(200);
        $response->assertHeader(CorrelationIdMiddleware::HEADER_NAME, $customCorrelationId);
    }

    public function test_standard_error_envelope_for_validation_errors(): void
    {
        // Missing required fields on branch store
        $response = $this->withHeaders([
            'Idempotency-Key' => 'test-idem-validation-1',
        ])->postJson('/api/v1/branches', [
            'organization_id' => $this->org->id,
            // brand_id, code, name missing
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'error' => [
                    'code',
                    'message',
                    'details',
                    'correlation_id',
                    'timestamp',
                ],
                'message',
                'errors',
            ]);

        $this->assertSame('VALIDATION_ERROR', $response->json('error.code'));
        $this->assertArrayHasKey('brand_id', $response->json('error.details'));
        $this->assertArrayHasKey('code', $response->json('error.details'));
        $this->assertArrayHasKey('name', $response->json('error.details'));
        $this->assertNotEmpty($response->json('error.correlation_id'));
    }

    public function test_standard_error_envelope_for_not_found_errors(): void
    {
        $fakeUuid = (string) Str::uuid7();
        $response = $this->getJson("/api/v1/branches/{$fakeUuid}");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'error' => [
                    'code',
                    'message',
                    'correlation_id',
                    'timestamp',
                ],
            ]);

        $this->assertSame('RESOURCE_NOT_FOUND', $response->json('error.code'));
        $this->assertNotEmpty($response->json('error.correlation_id'));
    }

    public function test_pagination_and_filtering_page_based(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            Branch::create([
                'organization_id' => $this->org->id,
                'brand_id' => $this->brand->id,
                'code' => sprintf('BR-%02d', $i),
                'name' => "Branch {$i}",
                'city' => $i % 2 === 0 ? 'Cairo' : 'Alexandria',
                'status' => 'active',
            ]);
        }

        // Request page 1 with 10 per page
        $response = $this->getJson('/api/v1/branches?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'pagination' => [
                        'type',
                        'current_page',
                        'per_page',
                        'total',
                        'total_pages',
                        'has_more',
                    ],
                ],
                'correlation_id',
            ]);

        $this->assertCount(10, $response->json('data'));
        $this->assertSame(1, $response->json('meta.pagination.current_page'));
        $this->assertSame(10, $response->json('meta.pagination.per_page'));
        $this->assertSame(25, $response->json('meta.pagination.total'));
        $this->assertSame(3, $response->json('meta.pagination.total_pages'));
        $this->assertTrue($response->json('meta.pagination.has_more'));

        // Request page 3 with 10 per page
        $page3Response = $this->getJson('/api/v1/branches?page=3&per_page=10');
        $page3Response->assertStatus(200);
        $this->assertCount(5, $page3Response->json('data'));
        $this->assertFalse($page3Response->json('meta.pagination.has_more'));
    }

    public function test_pagination_cursor_based(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Branch::create([
                'organization_id' => $this->org->id,
                'brand_id' => $this->brand->id,
                'code' => sprintf('CURSOR-%02d', $i),
                'name' => "Cursor Branch {$i}",
            ]);
        }

        $response = $this->getJson('/api/v1/branches?cursor=initial&per_page=2');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'pagination' => [
                        'type',
                        'per_page',
                        'has_more',
                    ],
                ],
            ]);

        $this->assertSame('cursor', $response->json('meta.pagination.type'));
        $this->assertSame(2, $response->json('meta.pagination.per_page'));
        $this->assertCount(2, $response->json('data'));
    }

    public function test_query_filter_search_and_status(): void
    {
        Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'ZAMALEK-01',
            'name' => 'Zamalek Island Roastery',
            'city' => 'Cairo',
            'status' => 'active',
        ]);

        Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'MAADI-02',
            'name' => 'Maadi Degla Roastery',
            'city' => 'Cairo',
            'status' => 'inactive',
        ]);

        Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'SMOUHA-03',
            'name' => 'Alexandria Smouha Roastery',
            'city' => 'Alexandria',
            'status' => 'active',
        ]);

        // Filter by city=Cairo
        $cairoRes = $this->getJson('/api/v1/branches?city=Cairo');
        $cairoRes->assertStatus(200);
        $this->assertCount(2, $cairoRes->json('data'));

        // Filter by search=Zamalek
        $searchRes = $this->getJson('/api/v1/branches?search=Zamalek');
        $searchRes->assertStatus(200);
        $this->assertCount(1, $searchRes->json('data'));
        $this->assertSame('ZAMALEK-01', $searchRes->json('data.0.code'));

        // Filter by status=inactive
        $inactiveRes = $this->getJson('/api/v1/branches?status=inactive');
        $inactiveRes->assertStatus(200);
        $this->assertCount(1, $inactiveRes->json('data'));
        $this->assertSame('MAADI-02', $inactiveRes->json('data.0.code'));
    }

    public function test_idempotency_key_is_required_for_idempotent_endpoints(): void
    {
        $response = $this->postJson('/api/v1/branches', [
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'TEST-01',
            'name' => 'Test Branch',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => [
                    'code' => 'IDEMPOTENCY_KEY_REQUIRED',
                ],
            ]);
    }

    public function test_idempotent_request_returns_success_and_caches_response(): void
    {
        $key = 'idem-branch-create-001';
        $payload = [
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'IDEM-01',
            'name' => 'Idempotent Branch 1',
            'city' => 'Cairo',
        ];

        // 1st request
        $response1 = $this->withHeaders([
            'Idempotency-Key' => $key,
        ])->postJson('/api/v1/branches', $payload);

        $response1->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'code' => 'IDEM-01',
                    'name' => 'Idempotent Branch 1',
                ],
            ]);

        $this->assertNull($response1->headers->get('X-Idempotent-Replay'));
        $createdBranchId = $response1->json('data.id');

        // Confirm database has exactly 1 record
        $this->assertDatabaseHas('branches', ['id' => $createdBranchId]);
        $this->assertDatabaseHas('idempotency_records', [
            'idempotency_key' => $key,
            'status' => IdempotencyRecord::STATUS_COMPLETED,
            'response_code' => 201,
        ]);

        // 2nd identical request with same key
        $response2 = $this->withHeaders([
            'Idempotency-Key' => $key,
        ])->postJson('/api/v1/branches', $payload);

        $response2->assertStatus(201)
            ->assertHeader('X-Idempotent-Replay', 'true')
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $createdBranchId,
                    'code' => 'IDEM-01',
                ],
            ]);

        // Ensure no second branch was created
        $this->assertSame(1, Branch::where('code', 'IDEM-01')->count());
    }

    public function test_idempotent_request_with_payload_mismatch_returns_conflict(): void
    {
        $key = 'idem-branch-create-002';
        $payloadOriginal = [
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'ORIG-01',
            'name' => 'Original Branch',
        ];

        $response1 = $this->withHeaders([
            'Idempotency-Key' => $key,
        ])->postJson('/api/v1/branches', $payloadOriginal);

        $response1->assertStatus(201);

        // Same key, different payload (different name and code)
        $payloadDifferent = [
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'DIFF-02',
            'name' => 'Completely Different Branch',
        ];

        $response2 = $this->withHeaders([
            'Idempotency-Key' => $key,
        ])->postJson('/api/v1/branches', $payloadDifferent);

        $response2->assertStatus(409)
            ->assertJson([
                'error' => [
                    'code' => 'IDEMPOTENCY_PAYLOAD_MISMATCH',
                ],
            ]);
    }

    public function test_idempotent_concurrent_request_in_progress_returns_conflict(): void
    {
        $key = 'idem-concurrent-001';
        $payload = [
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'CONC-01',
            'name' => 'Concurrent Branch',
        ];

        // Simulate an in-progress record in DB created 5 seconds ago
        $request = \Illuminate\Http\Request::create('/api/v1/branches', 'POST', $payload);
        $service = app(\App\Support\Http\IdempotencyService::class);
        $requestHash = $service->computeRequestHash($request);
        $scope = $service->resolveScope($request);

        IdempotencyRecord::create([
            'id' => (string) Str::uuid7(),
            'scope' => $scope,
            'idempotency_key' => $key,
            'request_hash' => $requestHash,
            'status' => IdempotencyRecord::STATUS_IN_PROGRESS,
            'locked_at' => Carbon::now()->subSeconds(5),
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        // Attempt concurrent request while in progress
        $response = $this->withHeaders([
            'Idempotency-Key' => $key,
        ])->postJson('/api/v1/branches', $payload);

        $response->assertStatus(409)
            ->assertJson([
                'error' => [
                    'code' => 'IDEMPOTENCY_CONCURRENT_REQUEST',
                ],
            ]);
    }

    public function test_failed_server_execution_allows_subsequent_retry(): void
    {
        $key = 'idem-retry-after-failure';
        $payload = [
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'RETRY-01',
            'name' => 'Retry Branch',
        ];

        $request = \Illuminate\Http\Request::create('/api/v1/branches', 'POST', $payload);
        $service = app(\App\Support\Http\IdempotencyService::class);
        $requestHash = $service->computeRequestHash($request);
        $scope = $service->resolveScope($request);

        // Pre-create a failed record
        IdempotencyRecord::create([
            'id' => (string) Str::uuid7(),
            'scope' => $scope,
            'idempotency_key' => $key,
            'request_hash' => $requestHash,
            'status' => IdempotencyRecord::STATUS_FAILED,
            'locked_at' => Carbon::now()->subMinute(),
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        // Next request with same key should be permitted to execute and succeed
        $response = $this->withHeaders([
            'Idempotency-Key' => $key,
        ])->postJson('/api/v1/branches', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('idempotency_records', [
            'idempotency_key' => $key,
            'status' => IdempotencyRecord::STATUS_COMPLETED,
        ]);
    }
}
