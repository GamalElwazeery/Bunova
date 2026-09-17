<?php

namespace App\Support\Http\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class IdempotencyConcurrentConflictException extends HttpException
{
    public function __construct(string $message = 'A request with this idempotency key is currently in progress. Please retry shortly.', ?\Throwable $previous = null)
    {
        parent::__construct(409, $message, $previous);
    }
}
