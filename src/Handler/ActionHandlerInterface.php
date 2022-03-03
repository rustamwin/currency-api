<?php

declare(strict_types=1);


namespace Rustamwin\CurrencyApi\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ActionHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface;
}