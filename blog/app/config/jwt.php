<?php

declare(strict_types=1);

use Ruvents\SpiralJwt\Keys;

return [
    'algorithm' => 'HS256',
    'expiresAt' => '+1 week',
    'key' => new Keys('secret'),
];
