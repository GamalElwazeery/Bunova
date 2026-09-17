<?php

namespace App\Domain\Shared\Services;

use App\Domain\Shared\Contracts\CommandHandler;
use App\Domain\Shared\Contracts\DomainCommand;
use App\Domain\Shared\Contracts\UseCaseResult;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class CommandBus
{
    /** @var array<class-string<DomainCommand>, class-string<CommandHandler>|CommandHandler> */
    protected array $handlers = [];

    /**
     * Register a handler for a command class.
     *
     * @param class-string<DomainCommand> $commandClass
     * @param class-string<CommandHandler>|CommandHandler $handler
     */
    public function register(string $commandClass, string|CommandHandler $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    /**
     * Dispatch a domain command wrapped in an atomic database transaction.
     *
     * @throws Throwable
     */
    public function dispatch(DomainCommand $command): UseCaseResult
    {
        $commandClass = get_class($command);

        if (!isset($this->handlers[$commandClass])) {
            throw new InvalidArgumentException("No handler registered for command [{$commandClass}].");
        }

        $handler = $this->handlers[$commandClass];
        if (is_string($handler)) {
            $handler = app($handler);
        }

        /** @var UseCaseResult $result */
        $result = DB::transaction(function () use ($handler, $command) {
            return $handler->handle($command);
        });

        return $result;
    }
}
