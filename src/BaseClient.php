<?php

namespace AlexKassel\ScrapingClients;

abstract class BaseClient
{
    protected string $endpoint;
    protected string $endpointAsync;

    public function __construct(
        protected string $api_key
    ) {}

    public function request(string $url): array
    {
        $data = [
            'microtime' => $microtime = microtime(true),
            'datetime' => date('Y-m-d H:i:s'),
            'methode' => 'request',
            'request' => $request = [
                'curl -X GET',
                '-D -',
                sprintf('"%s"', sprintf($this->endpoint, $this->api_key, urlencode($url))),
            ],
            'response' => [
                'url' => $url,
                'response' => (function () use ($request) {
                    $response = shell_exec(implode(' ', $request));
                    $response = array_combine(
                        ['headers', 'body'],
                        explode("\r\n\r\n", $response, 2)
                    );
                    $response['headers'] = (function () use ($response) {
                        $headers = [];
                        foreach (explode("\r\n", $response['headers']) as $line) {
                            if (2 === count($exploded = explode(':', $line, 2))) {
                                $headers[$exploded[0]] = trim($exploded[1]);
                            }
                        }

                        return $headers;
                    })();

                    if ($decoded = json_decode($response['body'], true)) {
                        $response['body'] = $decoded;
                    }

                    return $response;
                })(),
            ],
            'exec_time' => microtime(true) - $microtime,
        ];

        $this->cache($data);
        return $data['response']['response'];
    }

    public function requestAsync(string $url): array
    {
        $data = [
            'microtime' => $microtime = microtime(true),
            'datetime' => date('Y-m-d H:i:s'),
            'methode' => 'request',
            'request' => $request = [
                'curl -X POST',
                '-H "Content-Type: application/json"',
                '-d',
                sprintf("'%s'", json_encode([
                    'apiKey' => $this->api_key,
                    'url' => $url,
                ])),
                sprintf('"%s"', $this->endpointAsync),
            ],
            'response' => json_decode(shell_exec(implode(' ', $request)), true),
            'exec_time' => microtime(true) - $microtime,
        ];

        $this->cache($data);
        return $data['response'];
    }

    protected function cache(array $data): void
    {
        $relativePath = dirname((new \ReflectionClass($this))->getFileName());
        $cachePath = $relativePath . '/Cache';

        if (! is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }

        $fileName = sprintf('%s/%s.json', $cachePath, ($data['microtime'] ?? microtime(true)));
        file_put_contents($fileName, json_encode($data, JSON_PRETTY_PRINT));
    }
}
