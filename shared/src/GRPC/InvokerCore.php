<?php

declare(strict_types=1);

namespace Spiral\Shared\GRPC;

use Spiral\Core\CoreInterface;

class InvokerCore implements CoreInterface
{
    public function __construct(
        private \Spiral\RoadRunner\GRPC\Invoker $invoker,
    ) {
    }

    public function callAction(string $controller, string $action, array $parameters = [])
    {
        return $this->invoker->invoke(
            $parameters['service'],
            $parameters['method'],
            $parameters['ctx'],
            $parameters['input'],
        );
    }
}
