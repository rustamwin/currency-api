<?php

declare(strict_types=1);

namespace Rustamwin\CurrencyApi;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rustamwin\CurrencyApi\Handler\ActionHandlerInterface;

final class Application
{
    private ActionHandlerInterface $actionHandler;

    public function __construct(ActionHandlerInterface $actionHandler)
    {
        $this->actionHandler = $actionHandler;
    }

    public function run(): void
    {
        $request = ServerRequest::fromGlobals();
        $response = $this->actionHandler->handle($request);

        $this->emit($request, $response);
    }

    private function emit(ServerRequestInterface $request, ResponseInterface $response)
    {
        (new SapiEmitter())->emit($response, $request->getMethod() === 'HEAD');
    }
}