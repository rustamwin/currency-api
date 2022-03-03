<?php

declare(strict_types=1);

namespace Rustamwin\CurrencyApi\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ServeCommand extends Command
{
    protected static $defaultName = 'serve';
    protected static $defaultDescription = 'Runs PHP built-in web server';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $address = '127.0.0.1:8080';
        $router = 'public/index.php';
        $documentRoot = 'public';
        if ($this->isAddressTaken($address)) {
            $output->writeln("<bg=orange>Address http://$address is already taken");
            return 1;
        }
        if (!file_exists($router)) {
            $output->writeln('<bg=orange>Router not found</>');
            return 1;
        }
        passthru('"' . PHP_BINARY . '"' . " -S $address -t \"$documentRoot\" $router");

        return 0;
    }

    private function isAddressTaken(string $address): bool
    {
        [$hostname, $port] = explode(':', $address);
        $fp = @fsockopen($hostname, (int)$port, $errno, $errmsg, 3);

        if ($fp === false) {
            return false;
        }

        fclose($fp);
        return true;
    }
}