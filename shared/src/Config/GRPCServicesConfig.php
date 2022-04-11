<?php

declare(strict_types=1);

namespace Spiral\Shared\Config;

use Spiral\Core\InjectableConfig;

final class GRPCServicesConfig extends InjectableConfig
{
    public const CONFIG = 'grpcServices';

    protected $config = [
        'services' => [],
    ];

    public function getService(string $name): array
    {
        return $this->config['services'][$name] ?? [
            'host' => 'localhost'
        ];
    }
}
