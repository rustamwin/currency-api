<?php

declare(strict_types=1);

namespace Rustamwin\CurrencyApi;

use Psr\Http\Message\ResponseInterface;

final class SapiEmitter
{
    private const DEFAULT_BUFFER_SIZE = 8_388_608; // 8MB

    /**
     * @param ResponseInterface $response
     * @param bool $withoutBody
     *
     * @throws \Exception
     * @return void
     */
    public function emit(ResponseInterface $response, bool $withoutBody = false): void
    {
        $status = $response->getStatusCode();
        $withoutContentLength = $withoutBody || $response->hasHeader('Transfer-Encoding');

        if ($withoutContentLength) {
            $response = $response->withoutHeader('Content-Length');
        }

        if (headers_sent()) {
            throw new \Exception('Headers already sent.');
        }

        header_remove();

        foreach ($response->getHeaders() as $header => $values) {
            foreach ($values as $value) {
                header("$header: $value", false);
            }
        }

        header(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $status,
            $response->getReasonPhrase(),
        ), true, $status);

        if ($withoutBody) {
            return;
        }

        if (!$withoutContentLength && !$response->hasHeader('Content-Length')) {
            $contentLength = $response->getBody()->getSize();

            if ($contentLength !== null) {
                header("Content-Length: $contentLength", true);
            }
        }

        $this->emitBody($response);
    }

    private function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        while (!$body->eof()) {
            echo $body->read(self::DEFAULT_BUFFER_SIZE);
            flush();
        }
    }
}