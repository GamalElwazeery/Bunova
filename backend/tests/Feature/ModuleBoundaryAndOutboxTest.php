<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use App\Domain\Shared\Contracts\CommandHandler;
use App\Domain\Shared\Contracts\DomainCommand;
use App\Domain\Shared\Contracts\UseCaseResult;
use App\Domain\Shared\Models\OutboxEvent;
use App\Domain\Shared\Services\CommandBus;
use App\Domain\Shared\Services\OutboxService;
use App\Domain\Shared\ValueObjects\CanonicalId;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Sample Test Command
class PlaceTestOrderCommand extends DomainCommand
{
    public function __construct(
        string $organizationId,
        string $actorId,
        public readonly string $orderId,
        public readonly int $totalMinorUnits,
        ?string $branchId = null
    ) {
        parent::__construct(
            organizationId: $organizationId,
            actorId: $actorId,
            actorType: 'staff',
            branchId: $branchId
        );
    }
}

// Sample Test Command Handler
class PlaceTestOrderHandler implements CommandHandler
{
    public function __construct(
        protected OutboxService $outbox
    ) {}

    public function handle(DomainCommand $command): UseCaseResult
    {
        /** @var PlaceTestOrderCommand $command */

        // 1. Record outbox event inside the transaction
        $this->outbox->record(
            eventName: 'orders.order_placed',
            aggregateType: 'order',
            aggregateId: $command->orderId,
            organizationId: $command->organizationId,
            payload: [
                'order_id' => $command->orderId,
                'total_minor' => $command->totalMinorUnits,
            ],
            branchId: $command->branchId,
            actorId: $command->actorId,
            actorType: $command->actorType,
            correlationId: $command->correlationId
        );

        return UseCaseResult::ok(['order_id' => $command->orderId]);
    }
}

// Failing Test Command
class FailingTestCommand extends DomainCommand
{
    public function __construct(
        string $organizationId,
        string $actorId,
        public readonly string $orderId
    ) {
        parent::__construct(organizationId: $organizationId, actorId: $actorId);
    }
}

class FailingTestHandler implements CommandHandler
{
    public function __construct(
        protected OutboxService $outbox
    ) {}

    public function handle(DomainCommand $command): UseCaseResult
    {
        /** @var FailingTestCommand $command */

        // Record outbox event
        $this->outbox->record(
            eventName: 'orders.failed_attempt',
            aggregateType: 'order',
            aggregateId: $command->orderId,
            organizationId: $command->organizationId,
            payload: ['order_id' => $command->orderId]
        );

        // Intentionally throw exception to simulate failure mid-transaction
        throw new Exception("Simulated business rule violation");
    }
}

class ModuleBoundaryAndOutboxTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected Branch $branch;
    protected CommandBus $commandBus;
    protected OutboxService $outboxService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outboxService = app(OutboxService::class);
        $this->commandBus = new CommandBus();

        $this->commandBus->register(PlaceTestOrderCommand::class, PlaceTestOrderHandler::class);
        $this->commandBus->register(FailingTestCommand::class, FailingTestHandler::class);

        $this->org = Organization::create([
            'name' => 'Artisan Monolith Org',
            'slug' => 'artisan-monolith',
            'commercial_status' => 'active',
        ]);

        $brand = Brand::create([
            'organization_id' => $this->org->id,
            'name' => 'Artisan Concept',
            'slug' => 'artisan-concept',
        ]);

        $this->branch = Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $brand->id,
            'name' => 'Downtown Branch',
            'code' => 'DT-01',
            'status' => 'active',
        ]);
    }

    public function test_domain_command_execution_commits_outbox_event_atomically(): void
    {
        $orderId = CanonicalId::uuid7()->value();

        $command = new PlaceTestOrderCommand(
            organizationId: $this->org->id,
            actorId: 'STF-01',
            orderId: $orderId,
            totalMinorUnits: 15000,
            branchId: $this->branch->id
        );

        $result = $this->commandBus->dispatch($command);

        $this->assertTrue($result->success);
        $this->assertEquals($orderId, $result->data['order_id']);

        // Outbox event must exist in database with status pending
        $outboxEvent = OutboxEvent::where('aggregate_id', $orderId)->first();
        $this->assertNotNull($outboxEvent);
        $this->assertEquals('orders.order_placed', $outboxEvent->event_name);
        $this->assertEquals($this->org->id, $outboxEvent->organization_id);
        $this->assertEquals($this->branch->id, $outboxEvent->branch_id);
        $this->assertEquals('STF-01', $outboxEvent->actor_id);
        $this->assertEquals($command->correlationId, $outboxEvent->correlation_id);
        $this->assertEquals(OutboxEvent::STATUS_PENDING, $outboxEvent->status);
        $this->assertEquals(15000, $outboxEvent->payload['total_minor']);
    }

    public function test_failed_command_rolls_back_both_mutation_and_outbox_event(): void
    {
        $orderId = CanonicalId::uuid7()->value();

        $command = new FailingTestCommand(
            organizationId: $this->org->id,
            actorId: 'STF-02',
            orderId: $orderId
        );

        try {
            $this->commandBus->dispatch($command);
            $this->fail("Command was expected to throw an exception.");
        } catch (Exception $e) {
            $this->assertEquals("Simulated business rule violation", $e->getMessage());
        }

        // Outbox event must NOT exist because the transaction rolled back!
        $this->assertDatabaseMissing('outbox_events', [
            'aggregate_id' => $orderId,
        ]);
    }

    public function test_outbox_service_dispatches_pending_events(): void
    {
        $orderId1 = CanonicalId::uuid7()->value();
        $orderId2 = CanonicalId::uuid7()->value();

        $this->outboxService->record('orders.order_placed', 'order', $orderId1, $this->org->id, ['item' => 'Latte']);
        $this->outboxService->record('orders.order_placed', 'order', $orderId2, $this->org->id, ['item' => 'Cappuccino']);

        $pending = $this->outboxService->getPending();
        $this->assertCount(2, $pending);

        // Dispatch
        $dispatchedCount = $this->outboxService->dispatchPending();
        $this->assertEquals(2, $dispatchedCount);

        // Verify status transitioned to dispatched
        $events = OutboxEvent::whereIn('aggregate_id', [$orderId1, $orderId2])->get();
        foreach ($events as $event) {
            $this->assertTrue($event->isDispatched());
            $this->assertNotNull($event->dispatched_at);
        }

        // No more pending events
        $this->assertCount(0, $this->outboxService->getPending());
    }

    public function test_outbox_service_handles_dispatch_failures_resiliently(): void
    {
        $orderId = CanonicalId::uuid7()->value();
        $event = $this->outboxService->record('orders.faulty', 'order', $orderId, $this->org->id, []);

        // Custom dispatcher that throws
        $faultyDispatcher = function () {
            throw new Exception("Connection to downstream message broker timed out");
        };

        $dispatchedCount = $this->outboxService->dispatchPending($faultyDispatcher);
        $this->assertEquals(0, $dispatchedCount);

        $event->refresh();
        $this->assertEquals(OutboxEvent::STATUS_FAILED, $event->status);
        $this->assertEquals(1, $event->attempts);
        $this->assertStringContainsString("downstream message broker", $event->last_error);
    }
}
