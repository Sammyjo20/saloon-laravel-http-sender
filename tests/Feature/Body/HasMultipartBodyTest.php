<?php

declare(strict_types=1);

use Saloon\Data\MultipartValue;
use Saloon\Http\Faking\MockResponse;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\FulfilledPromise;
use Saloon\HttpSender\Tests\Fixtures\Connectors\HttpSenderConnector;
use Saloon\HttpSender\Tests\Fixtures\Requests\HasMultipartBodyRequest;

test('the default body is loaded', function () {
    $request = new HasMultipartBodyRequest();

    expect($request->body()->get())->toEqual([
        'nickname' => new MultipartValue('nickname', 'Sam', 'user.txt', ['X-Saloon' => 'Yee-haw!']),
    ]);
});

test('the http sender properly sends it', function () {
    $connector = new HttpSenderConnector;
    $request = new HasMultipartBodyRequest;

    $connector->sender()->addMiddleware(function (callable $handler) use ($request) {
        return function (RequestInterface $guzzleRequest, array $options) use ($request) {
            expect($guzzleRequest->getHeader('Content-Type')[0])->toContain('multipart/form-data; boundary=');
            expect((string)$guzzleRequest->getBody())->toContain(
                'X-Saloon: Yee-haw!',
                'Content-Disposition: form-data; name="nickname"; filename="user.txt"',
                'Content-Length: 3',
                'Sam',
            );

            return new FulfilledPromise(MockResponse::make()->getPsrResponse());
        };
    });

    $connector->send($request);
});
