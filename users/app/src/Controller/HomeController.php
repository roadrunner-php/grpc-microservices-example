<?php

/**
 * This file is part of Spiral package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Job\Ping;
use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Queue\QueueInterface;

class HomeController
{
    use PrototypeTrait;

    public function __construct(
        private QueueInterface $queue,
    ) {
    }

    public function index(): string
    {
        return $this->views->render('home.dark.php');
    }
}
