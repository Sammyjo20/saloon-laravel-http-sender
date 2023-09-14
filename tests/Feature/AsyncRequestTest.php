<?php

declare(strict_types=1);

use Saloon\Http\Response;
use Saloon\HttpSender\HttpSender;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use GuzzleHttp\Promise\PromiseInterface;
use Saloon\Exceptions\Request\RequestException;
use Saloon\HttpSender\Tests\Fixtures\Responses\UserData;
use Saloon\HttpSender\Tests\Fixtures\Requests\UserRequest;
use Saloon\HttpSender\Tests\Fixtures\Requests\ErrorRequest;
use Saloon\HttpSender\Tests\Fixtures\Responses\UserResponse;
use Saloon\HttpSender\Tests\Fixtures\Connectors\HttpSenderConnector;
use Saloon\HttpSender\Tests\Fixtures\Requests\UserRequestWithCustomResponse;

test('an asynchronous request can be made successfully', function () {
    $promise = HttpSenderConnector::make()->sendAsync(new UserRequest);

    expect($promise)->toBeInstanceOf(PromiseInterface::class);

    $response = $promise->wait();

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->getConnector()->sender())->toBeInstanceOf(HttpSender::class);

    $data = $response->json();

    expect($response->getPendingRequest()->isAsynchronous())->toBeTrue();
    expect($response->isMocked())->toBeFalse();
    expect($response->status())->toEqual(200);

    expect($data)->toEqual([
        'name' => 'Sammyjo20',
        'actual_name' => 'Sam',
        'twitter' => '@carre_sam',
    ]);
});

test('an asynchronous request can handle an exception properly', function () {
    $promise = HttpSenderConnector::make()->sendAsync(new ErrorRequest);

    $this->expectException(RequestException::class);

    $promise->wait();
});

test('an asynchronous response will still be passed through response middleware', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam']),
    ]);

    $request = new UserRequest();

    Response::macro('setValue', function ($value) {
        $this->value = $value;
    });

    Response::macro('getValue', function () {
        return $this->value;
    });

    $request->middleware()->onResponse(function (Response $response) {
        $response->setValue(true);
    });

    $connector = new HttpSenderConnector;

    $promise = $connector->sendAsync($request, $mockClient);
    $response = $promise->wait();

    expect($response->getValue())->toBeTrue();
});

test('an asynchronous request will return a custom response', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar']),
    ]);

    $connector = new HttpSenderConnector;
    $request = new UserRequestWithCustomResponse();

    $promise = $connector->sendAsync($request, $mockClient);

    $response = $promise->wait();

    expect($response)->toBeInstanceOf(UserResponse::class);
    expect($response)->customCastMethod()->toBeInstanceOf(UserData::class);
    expect($response)->foo()->toBe('bar');
});
