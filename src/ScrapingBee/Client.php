<?php # 418

declare(strict_types=1);

namespace AlexKassel\ScrapingClients\ScrapingBee;

use AlexKassel\ScrapingClients\BaseClient;

class Client extends BaseClient
{
    protected string $endpoint = 'https://app.scrapingbee.com/api/v1/?api_key=%s&url=%s';
}
