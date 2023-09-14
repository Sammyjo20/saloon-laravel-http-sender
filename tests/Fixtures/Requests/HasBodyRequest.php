<?php

declare(strict_types=1);

namespace Saloon\HttpSender\Tests\Fixtures\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody as HasBodyContract;
use Saloon\Traits\Body\HasStringBody;

class HasBodyRequest extends Request implements HasBodyContract
{
    use HasStringBody;

    /**
     * Define the method that the request will use.
     */
    protected Method $method = Method::GET;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    protected function defaultBody(): ?string
    {
        return 'name: Sam';
    }
}
