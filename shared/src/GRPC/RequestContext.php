<?php

declare(strict_types=1);

namespace Spiral\Shared\GRPC;

use Spiral\Auth\TokenInterface;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Spiral\RoadRunner\GRPC\ResponseHeaders;

final class RequestContext implements ContextInterface
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        private array $values = []
    ) {
    }

    public function withToken(?string $token, string $key = 'token'): ContextInterface
    {
        if ($token === null) {
            return $this;
        }

        $metadata = $this->getValue('metadata', []);
        $metadata[$key] = [$token];

        return $this->withMetadata($metadata);
    }

    public function getToken(string $key = 'token'): ?string
    {
        return $this->getValue('metadata', [])[$key] ?? null;
    }

    public function withMetadata(array $metadata): ContextInterface
    {
        return $this->withValue('metadata', $metadata);
    }

    public function withOptions(array $metadata): ContextInterface
    {
        return $this->withValue('options', $metadata);
    }

    public function withValue(string $key, $value): ContextInterface
    {
        $ctx = clone $this;
        $ctx->values[$key] = $value;

        return $ctx;
    }

    public function getValue(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
