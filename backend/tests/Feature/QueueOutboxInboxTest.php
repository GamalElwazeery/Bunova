<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\TenantContext;
use App\Domain\Shared\Jobs\ProcessOutboxJob;
use App\Domain\Shared\Models\InboxMessage;
use App\Domain\Shared\Models\OutboxEvent;
use App\Domain\Shared\Services\InboxService;
use App\Domain\Shared\Services\OutboxService;
use App\Support\Jobs\DomainJob;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class QueueOutboxInboxTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected Brand $brand;
    protected Branch $branch;
    protected InboxService $inboxService;
    protected OutboxService $outboxService;

    protected function setUp(): void
    {
        parent::setUp();

        TenantContext::clear();

        $this->inboxService = app(InboxService::class);
        $this->outboxService = app(OutboxService::class);

        $this->org = Organization::create([
            'name' => 'Queue Org',
            'slug' => 'queue-org',
            'commercial_status' => 'active',
        ]);

        $this->brand = Brand::create([
            'organization_id' => $this->org->id,
            'name' => 'Queue Brand',
            'slug' => 'queue-brand',
        ]);

        $this->branch = Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'Q-01',
            'name' => 'Queue Branch',
            'status' => 'active',
        ]);
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
        parent::tearDown();
    }

    public function test_inbox_service_executes_event_consumer_successfully_first_time(): void
    {
        $messageId = 'evt-order-created-001';
        $consumerName = 'StockConsumptionConsumer';
        $executionCount = 0;

        $result = $this->inboxService->executeTransactionally(
            consumerName: $consumerName,
            messageId: $messageId,
            eventName: 'order.created',
            payload: ['order_id' => '123', 'quantity' => 2],
            handler: function (array $payload) use (&$executionCount) {
                $executionCount++;
                return 'stock_decremented_by_'.$payload['quantity'];
            }
        );

        $this->assertSame('stock_decremented_by_2', $result);
        $this->assertSame(1, $executionCount);
        $this->assertTrue($this->inboxService->hasProcessed($consumerName, $messageId));

        $this->assertDatabaseHas('inbox_messages', [
            'consumer_name' => $consumerName,
            'message_id' => $messageId,
            'status' => InboxMessage::STATUS_PROCESSED,
        ]);
    }

    public function test_inbox_service_skips_duplicate_message_preventing_duplicate_side_effect(): void
    {
        $messageId = 'evt-payment-captured-777';
        $consumerName = 'FiscalReportingConsumer';
        $executionCounter = 0;

        $handler = function (array $payload) use (&$executionCounter) {
            $executionCounter++;
            return 'processed';
        };

        // 1st delivery
        $res1 = $this->inboxService->executeTransactionally(
            consumerName: $consumerName,
            messageId: $messageId,
            eventName: 'payment.captured',
            payload: ['payment_id' => 'pay-1', 'amount' => 5000],
            handler: $handler
        );

        $this->assertSame('processed', $res1);
        $this->assertSame(1, $executionCounter);

        // 2nd duplicate delivery of same event
        $res2 = $this->inboxService->executeTransactionally(
            consumerName: $consumerName,
            messageId: $messageId,
            eventName: 'payment.captured',
            payload: ['payment_id' => 'pay-1', 'amount' => 5000],
            handler: $handler
        );

        // Side effect must NOT run a second time
        $this->assertNull($res2);
        $this->assertSame(1, $executionCounter);
    }

    public function test_inbox_service_allows_retry_after_handler_failure(): void
    {
        $messageId = 'evt-retry-001';
        $consumerName = 'ExternalSyncConsumer';
        $attempts = 0;

        $failingHandler = function (array $payload) use (&$attempts) {
            $attempts++;
            if ($attempts === 1) {
                throw new RuntimeException('Transient gateway timeout');
            }
            return 'recovered_ok';
        };

        // 1st attempt fails
        try {
            $this->inboxService->executeTransactionally(
                consumerName: $consumerName,
                messageId: $messageId,
                eventName: 'item.synced',
                payload: ['id' => 'item-1'],
                handler: $failingHandler
            );
            $this->fail('Expected exception was not thrown');
        } catch (RuntimeException $e) {
            $this->assertSame('Transient gateway timeout', $e->getMessage());
        }

        $this->assertDatabaseHas('inbox_messages', [
            'consumer_name' => $consumerName,
            'message_id' => $messageId,
            'status' => InboxMessage::STATUS_FAILED,
            'error_message' => 'Transient gateway timeout',
        ]);

        // 2nd attempt succeeds
        $result = $this->inboxService->executeTransactionally(
            consumerName: $consumerName,
            messageId: $messageId,
            eventName: 'item.synced',
            payload: ['id' => 'item-1'],
            handler: $failingHandler
        );

        $this->assertSame('recovered_ok', $result);
        $this->assertSame(2, $attempts);
        $this->assertTrue($this->inboxService->hasProcessed($consumerName, $messageId));
    }

    public function test_inbox_pruning_deletes_only_aged_processed_records(): void
    {
        // Old processed record (40 days ago)
        InboxMessage::create([
            'id' => (string) Str::uuid7(),
            'consumer_name' => 'OldConsumer',
            'message_id' => 'old-msg-1',
            'event_name' => 'test.event',
            'status' => InboxMessage::STATUS_PROCESSED,
            'processed_at' => Carbon::now()->subDays(40),
        ]);

        // Recent processed record (5 days ago)
        InboxMessage::create([
            'id' => (string) Str::uuid7(),
            'consumer_name' => 'RecentConsumer',
            'message_id' => 'recent-msg-1',
            'event_name' => 'test.event',
            'status' => InboxMessage::STATUS_PROCESSED,
            'processed_at' => Carbon::now()->subDays(5),
        ]);

        // Failed record (should never be deleted by prune)
        InboxMessage::create([
            'id' => (string) Str::uuid7(),
            'consumer_name' => 'FailedConsumer',
            'message_id' => 'failed-msg-1',
            'event_name' => 'test.event',
            'status' => InboxMessage::STATUS_FAILED,
            'processed_at' => null,
        ]);

        $prunedCount = $this->inboxService->prune(retentionDays: 30);

        $this->assertSame(1, $prunedCount);
        $this->assertDatabaseMissing('inbox_messages', ['message_id' => 'old-msg-1']);
        $this->assertDatabaseHas('inbox_messages', ['message_id' => 'recent-msg-1']);
        $this->assertDatabaseHas('inbox_messages', ['message_id' => 'failed-msg-1']);
    }

    public function test_domain_job_restores_tenant_context_and_correlation_id(): void
    {
        $testJob = new class($this->org->id, $this->branch->id, 'trace-job-xyz') extends DomainJob {
            public bool $executed = false;
            public ?string $observedOrg = null;
            public ?string $observedBranch = null;

            public function handle(): void
            {
                $this->restoreContext();
                $this->executed = true;
                $this->observedOrg = TenantContext::getOrganizationId();
                $this->observedBranch = TenantContext::getBranchId();
            }
        };

        // Clear context before running job
        TenantContext::clear();
        $this->assertNull(TenantContext::getOrganization());

        // Execute job handle
        $testJob->handle();

        $this->assertTrue($testJob->executed);
        $this->assertSame($this->org->id, $testJob->observedOrg);
        $this->assertSame($this->branch->id, $testJob->observedBranch);
    }

    public function test_process_outbox_job_dispatches_pending_events(): void
    {
        // Record 2 pending outbox events
        $this->outboxService->record(
            eventName: 'catalog.product_updated',
            aggregateType: 'Product',
            aggregateId: 'prod-001',
            organizationId: $this->org->id,
            payload: ['name' => 'Updated Espresso']
        );

        $this->outboxService->record(
            eventName: 'catalog.product_updated',
            aggregateType: 'Product',
            aggregateId: 'prod-002',
            organizationId: $this->org->id,
            payload: ['name' => 'Updated Latte']
        );

        $job = new ProcessOutboxJob(batchSize: 10);
        $dispatched = $job->handle($this->outboxService);

        $this->assertSame(2, $dispatched);
        $this->assertSame(0, OutboxEvent::withoutGlobalScopes()->where('status', OutboxEvent::STATUS_PENDING)->count());
        $this->assertSame(2, OutboxEvent::withoutGlobalScopes()->where('status', OutboxEvent::STATUS_DISPATCHED)->count());
    }

    public function test_artisan_commands(): void
    {
        // Test outbox:process command
        $this->outboxService->record(
            eventName: 'order.closed',
            aggregateType: 'Order',
            aggregateId: 'ord-999',
            organizationId: $this->org->id,
            payload: ['total' => 1000]
        );

        $this->artisan('outbox:process', ['--limit' => 10])
            ->expectsOutputToContain('Successfully dispatched 1 outbox events.')
            ->assertSuccessful();

        // Test inbox:prune command
        InboxMessage::create([
            'id' => (string) Str::uuid7(),
            'consumer_name' => 'OldCLI',
            'message_id' => 'cli-old-1',
            'event_name' => 'cli.event',
            'status' => InboxMessage::STATUS_PROCESSED,
            'processed_at' => Carbon::now()->subDays(60),
        ]);

        $this->artisan('inbox:prune', ['--days' => 30])
            ->expectsOutputToContain('Successfully pruned 1 old inbox messages.')
            ->assertSuccessful();
    }
}
