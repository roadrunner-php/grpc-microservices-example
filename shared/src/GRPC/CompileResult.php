<?php

declare(strict_types=1);

namespace Spiral\Shared\GRPC;

final class CompileResult
{
    public function __construct(
        private array $files,
        private array $services
    ) {
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getServices(): array
    {
        return $this->services;
    }
}
