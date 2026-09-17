<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\TenantContext;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        TenantContext::clear();
        parent::tearDown();
    }

    public function test_can_create_organization_with_uuid(): void
    {
        $org = Organization::create([
            'slug' => 'roastery-cairo',
            'name' => 'Cairo Specialty Roasters',
            'legal_name' => 'Cairo Specialty Roasters LLC',
            'tax_identifier' => 'EG-123456789',
            'default_currency' => 'EGP',
            'default_timezone' => 'Africa/Cairo',
            'default_locale' => 'ar',
            'settings' => ['tax_inclusive' => true],
        ]);

        $this->assertNotEmpty($org->id);
        $this->assertTrue(Str::isUuid($org->id));
        $this->assertEquals('roastery-cairo', $org->slug);
        $this->assertEquals('EGP', $org->default_currency);
        $this->assertTrue($org->isActive());
        $this->assertTrue($org->settings['tax_inclusive']);
    }

    public function test_can_create_brand_and_branch_with_explicit_keys(): void
    {
        $org = Organization::create([
            'slug' => 'artisan-cafe',
            'name' => 'Artisan Café',
        ]);

        $brand = Brand::create([
            'organization_id' => $org->id,
            'slug' => 'main-brand',
            'name' => 'Artisan Express',
        ]);

        $branch = Branch::create([
            'organization_id' => $org->id,
            'brand_id' => $brand->id,
            'code' => 'CAI-01',
            'name' => 'Zamalek Branch',
            'timezone' => 'Africa/Cairo',
            'city' => 'Cairo',
            'country' => 'EG',
        ]);

        $this->assertTrue(Str::isUuid($brand->id));
        $this->assertTrue(Str::isUuid($branch->id));
        $this->assertEquals($org->id, $brand->organization_id);
        $this->assertEquals($org->id, $branch->organization_id);
        $this->assertEquals($brand->id, $branch->brand_id);

        $this->assertEquals($org->id, $branch->organization->id);
        $this->assertEquals($brand->id, $branch->brand->id);
    }

    public function test_branch_code_is_unique_per_organization(): void
    {
        $org1 = Organization::create(['slug' => 'org-one', 'name' => 'Org One']);
        $brand1 = Brand::create(['organization_id' => $org1->id, 'slug' => 'brand-1', 'name' => 'Brand 1']);

        Branch::create([
            'organization_id' => $org1->id,
            'brand_id' => $brand1->id,
            'code' => 'BRANCH-01',
            'name' => 'First Branch',
        ]);

        // Duplicate code in same organization must fail
        $this->expectException(QueryException::class);
        Branch::create([
            'organization_id' => $org1->id,
            'brand_id' => $brand1->id,
            'code' => 'BRANCH-01',
            'name' => 'Duplicate Code Branch',
        ]);
    }

    public function test_same_branch_code_is_allowed_in_different_organizations(): void
    {
        $org1 = Organization::create(['slug' => 'org-alpha', 'name' => 'Org Alpha']);
        $org2 = Organization::create(['slug' => 'org-beta', 'name' => 'Org Beta']);

        $brand1 = Brand::create(['organization_id' => $org1->id, 'slug' => 'b1', 'name' => 'B1']);
        $brand2 = Brand::create(['organization_id' => $org2->id, 'slug' => 'b2', 'name' => 'B2']);

        $b1 = Branch::create([
            'organization_id' => $org1->id,
            'brand_id' => $brand1->id,
            'code' => 'BRANCH-01',
            'name' => 'Alpha Branch 01',
        ]);

        $b2 = Branch::create([
            'organization_id' => $org2->id,
            'brand_id' => $brand2->id,
            'code' => 'BRANCH-01',
            'name' => 'Beta Branch 01',
        ]);

        $this->assertNotEquals($b1->organization_id, $b2->organization_id);
        $this->assertEquals($b1->code, $b2->code);
    }

    public function test_tenant_context_isolates_brand_and_branch_queries(): void
    {
        $orgA = Organization::create(['slug' => 'org-a', 'name' => 'Org A']);
        $orgB = Organization::create(['slug' => 'org-b', 'name' => 'Org B']);

        $brandA = Brand::create(['organization_id' => $orgA->id, 'slug' => 'brand-a', 'name' => 'Brand A']);
        $brandB = Brand::create(['organization_id' => $orgB->id, 'slug' => 'brand-b', 'name' => 'Brand B']);

        $branchA = Branch::create([
            'organization_id' => $orgA->id,
            'brand_id' => $brandA->id,
            'code' => 'A-01',
            'name' => 'Branch A1',
        ]);
        $branchB = Branch::create([
            'organization_id' => $orgB->id,
            'brand_id' => $brandB->id,
            'code' => 'B-01',
            'name' => 'Branch B1',
        ]);

        // In Org A context
        TenantContext::setOrganization($orgA);
        $brandsForA = Brand::all();
        $branchesForA = Branch::all();

        $this->assertCount(1, $brandsForA);
        $this->assertEquals($brandA->id, $brandsForA->first()->id);
        $this->assertCount(1, $branchesForA);
        $this->assertEquals($branchA->id, $branchesForA->first()->id);

        // In Org B context
        TenantContext::setOrganization($orgB);
        $brandsForB = Brand::all();
        $branchesForB = Branch::all();

        $this->assertCount(1, $brandsForB);
        $this->assertEquals($brandB->id, $brandsForB->first()->id);
        $this->assertCount(1, $branchesForB);
        $this->assertEquals($branchB->id, $branchesForB->first()->id);

        // Without tenancy bypass
        TenantContext::clear();
        $this->assertCount(2, Brand::all());
        $this->assertCount(2, Branch::all());
    }

    public function test_soft_deleting_branch_preserves_audit_history(): void
    {
        $org = Organization::create(['slug' => 'history-org', 'name' => 'History Org']);
        $brand = Brand::create(['organization_id' => $org->id, 'slug' => 'history-brand', 'name' => 'History Brand']);
        $branch = Branch::create([
            'organization_id' => $org->id,
            'brand_id' => $brand->id,
            'code' => 'HIST-01',
            'name' => 'Historic Branch',
        ]);

        $branch->delete();

        $this->assertSoftDeleted('branches', ['id' => $branch->id]);
        $this->assertCount(0, Branch::all());
        $this->assertCount(1, Branch::withTrashed()->get());
    }
}
