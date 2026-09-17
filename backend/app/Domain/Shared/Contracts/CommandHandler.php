<?php

namespace App\Domain\Shared\Contracts;

interface CommandHandler
{
    /**
     * Handle execution of a domain command.
     */
    public function handle(DomainCommand $command): UseCaseResult;
}
