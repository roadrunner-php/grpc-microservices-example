<?php

declare(strict_types=1);

namespace Spiral\Shared\GRPC\Interceptors;

use Spiral\Attributes\ReaderInterface;
use Spiral\Core\Container;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;
use Spiral\Core\InterceptableCore;
use Spiral\Shared\Attributes\InjectInterceptor;

final class InjectableInterceptor implements CoreInterceptorInterface
{
    public function __construct(
        private Container $container,
        private ReaderInterface $reader,
        private InterceptableCore $core
    ) {
    }

    public function process(string $controller, string $action, array $parameters, CoreInterface $core)
    {
        $refl = new \ReflectionClass($controller);

        /** @var InjectInterceptor[] $injectors */
        $injectors = $this->reader->getFunctionMetadata($refl->getMethod($action), InjectInterceptor::class);

        foreach ($injectors as $injector) {
            $this->core->addInterceptor($this->container->get($injector->getClass()));
        }

        return $core->callAction($controller, $action, $parameters);
    }
}
