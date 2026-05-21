<?php
declare(strict_types=1);

namespace App\Console\Command;

use App\Core\Container;

abstract class Command
{
    public function __construct(protected readonly Container $container)
    {
    }

    /** @param list<string> $args */
    abstract public function run(array $args): int;
}
