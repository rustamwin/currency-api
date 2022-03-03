<?php

declare(strict_types=1);

namespace Rustamwin\CurrencyApi\Handler;

use Cycle\Database\Table;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rustamwin\CurrencyApi\Command\CurrencySyncCommand;
use Rustamwin\CurrencyApi\DB;

final class CurrencyApiHandler implements ActionHandlerInterface
{
    /**
     * @throws \JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dbal = DB::getDbal();
        $response = new Response(
            200,
            [
                'Content-Type' => 'application/json',
            ]
        );
        /** @var Table $table */
        $table = $dbal->database()->table(CurrencySyncCommand::CURRENCY_TABLE);
        $data = $table->select()->fetchAll();

        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));
        return $response;
    }
}