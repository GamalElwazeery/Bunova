<?php

namespace App\Support\Http\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class IdempotencyKeyRequiredException extends HttpException
{
    public function __construct(string $message = 'Idempotency-Key header is required for this operation.', ?\Throwable $previous = null)
    {
        parent::__construct(400, $message, $previous);
    }
}
