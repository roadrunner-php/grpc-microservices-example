<?php

declare(strict_types=1);

namespace Spiral\Shared\Attributes;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

#[Attribute(Attribute::TARGET_METHOD), NamedArgumentConstructor]
class Guarded
{
    public function __construct(
        private string $tokenField = 'token'
    ) {
    }

    public function getTokenField(): string
    {
        return $this->tokenField;
    }
}
