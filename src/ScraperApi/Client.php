<?php

declare(strict_types=1);

namespace AlexKassel\ScrapingClients\ScraperApi;

use AlexKassel\ScrapingClients\BaseClient;

class Client extends BaseClient
{
    protected string $endpoint = 'http://api.scraperapi.com?api_key=%s&url=%s';
    protected string $endpointAsync = 'https://async.scraperapi.com/jobs';
}
