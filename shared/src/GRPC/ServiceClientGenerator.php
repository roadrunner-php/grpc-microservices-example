<?php

declare(strict_types=1);

namespace Spiral\Shared\GRPC;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;
use Spiral\Core\InterceptableCore;
use Spiral\Files\FilesInterface;
use Spiral\RoadRunner\GRPC\ContextInterface;

use Spiral\Shared\GRPC\Interceptors\CallRequestInterceptor;

use Spiral\Shared\GRPC\Interceptors\ValidateStatusInterceptor;

use function Sodium\add;

final class ServiceClientGenerator
{
    public function __construct(
        private FilesInterface $files
    ) {
    }

    /**
     * @param string $interfacePath
     * @return array{0: ParsedClass, 1: ParsedClass}
     */
    public function generate(string $interfacePath): array
    {
        $interface = new ParsedClass($this->files->read($interfacePath));

        $file = new PhpFile;
        $file->setStrictTypes();

        $client = new PhpNamespace($interface->getNamespace());
        $file->addNamespace($client);
        $clientClass = $client->addClass(str_replace('Interface', 'Client', $interface->getClassName()));

        $clientClass->addImplement($interface->getClassNameWithNamespace());

        $constructor = $clientClass->addMethod('__construct');
        $constructor->addPromotedParameter('core')
            ->setType(InterceptableCore::class)
            ->setPrivate();

        foreach ($interface->getMethods() as $method) {
            $clientMethod = $clientClass->addMethod($method->getName());
            $clientMethod->setParameters($method->getParameters());
            $clientMethod->setReturnType($method->getReturnType());

            $clientMethod->addBody(
                \sprintf(
                    <<<'EOL'
return $this->core->callAction(%s::class, '/'.self::NAME.'/%s', [
    'in' => $in,
    'responseClass' => %s::class,
    'ctx' => $ctx,
]);
EOL,
                    $interface->getClassName(),
                    $method->getName(),
                    $clientMethod->getReturnType()
                )
            );
        }

        $client->addUse(ContextInterface::class)
            ->addUse(InterceptableCore::class)
            ->addUse(CallRequestInterceptor::class)
            ->addUse(ValidateStatusInterceptor::class);

        $this->files->write(
            str_replace('Interface.php', 'Client.php', $interfacePath),
            $client = (new Printer)->printFile($file)
        );

        return [$interface, new ParsedClass($client)];
    }
}
