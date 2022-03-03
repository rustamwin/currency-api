<?php

declare(strict_types=1);

namespace Rustamwin\CurrencyApi\Command;

use Cycle\Database\DatabaseManager;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Table;
use GuzzleHttp\Client;
use Rustamwin\CurrencyApi\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CurrencySyncCommand extends Command
{
    public const CURRENCY_TABLE = 'currency';
    private const CURRENCY_API = 'https://nbu.uz/exchange-rates/json/';

    private ?DatabaseManager $dbal = null;
    protected static $defaultName = 'currency/sync';
    protected static $defaultDescription = 'Fetch and save currencies';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureSchemaCreated();

        $client = $this->createClient();

        $response = $client->get('');
        $result = json_decode(
            json: $response->getBody()->getContents(),
            flags: JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR
        );

        $this->saveCurrencies($result);

        $output->writeln('<bg=green>Currencies are synced successfully</>');

        return 0;
    }

    private function ensureSchemaCreated(): void
    {
        $dbal = DB::getDbal();
        if (!$dbal->database()->hasTable(self::CURRENCY_TABLE)) {
            /** @var AbstractTable $schema */
            $schema = $dbal->database()->table(self::CURRENCY_TABLE)->getSchema();

            $schema->primary('id');
            $schema->string('title', 191)->nullable(false);
            $schema->string('code', 32)->nullable(false);
            $schema->string('cb_price', 32)->nullable(false);
            $schema->datetime('date')->nullable(false);

            $schema->save();
        }
    }

    private function createClient(): Client
    {
        return new Client(
            [
                'base_uri' => self::CURRENCY_API,
            ]
        );
    }

    private function saveCurrencies(array $currencies): void
    {
        /** @var Table $table */
        $table = DB::getDbal()->database()->table(self::CURRENCY_TABLE);

        foreach ($currencies as $currency) {
            unset($currency['nbu_buy_price'], $currency['nbu_cell_price']);
            if (!isset($currency['code'])) {
                continue;
            }
            $currency['date'] = new \DateTime($currency['date']);
            if ($table->select('code')->where(['code' => $currency['code']])->count() > 0) {
                $table->update($currency, ['code' => $currency['code']])->run();
            } else {
                $table->insertOne($currency);
            }
        }
    }
}