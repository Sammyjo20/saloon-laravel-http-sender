<?php

declare(strict_types=1);

namespace Saloon\HttpSender;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;

/**
 * @property \Psr\Http\Message\StreamInterface|string $pendingBody
 */
class HttpPendingRequest extends PendingRequest
{
    /**
     * Constructor
     *
     * @param array<int, callable> $middleware
     */
    public function __construct(?Factory $factory = null, array $middleware = [])
    {
        parent::__construct($factory, $middleware);

        $this->options = [];
    }

    /**
     * Set the pending body on the HTTP request.
     *
     * @param \Psr\Http\Message\StreamInterface|string $pendingBody
     * @return $this
     */
    public function setPendingBody(mixed $pendingBody): static
    {
        $this->pendingBody = $pendingBody;

        return $this;
    }
}
