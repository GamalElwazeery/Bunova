<?php

namespace App\Support\Http\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class IdempotencyPayloadMismatchException extends HttpException
{
    public function __construct(string $message = 'Idempotency key was previously used with a materially different payload.', ?\Throwable $previous = null)
    {
        parent::__construct(409, $message, $previous);
    }
}
