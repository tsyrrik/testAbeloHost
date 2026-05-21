<?php
declare(strict_types=1);

namespace App\Console;

use App\Console\Command\Command;
use App\Core\Container;
use Throwable;

final class Application
{
    /** @var array<string, class-string<Command>> */
    private array $commands = [];

    public function __construct(private readonly Container $container)
    {
    }

    public function register(string $name, string $class): void
    {
        $this->commands[$name] = $class;
    }

    /** @param list<string> $argv */
    public function run(array $argv): int
    {
        $name = $argv[0] ?? null;
        if ($name === null || in_array($name, ['help', '--help', '-h'], true)) {
            $this->printHelp();
            return 0;
        }
        if (!isset($this->commands[$name])) {
            fwrite(STDERR, "Unknown command: {$name}\n\n");
            $this->printHelp();
            return 1;
        }

        $class = $this->commands[$name];
        /** @var Command $command */
        $command = new $class($this->container);

        try {
            return $command->run(array_slice($argv, 1));
        } catch (Throwable $e) {
            fwrite(STDERR, $e->getMessage() . "\n");
            return 1;
        }
    }

    private function printHelp(): void
    {
        echo "Usage: php bin/console <command> [options]\n\nCommands:\n";
        foreach (array_keys($this->commands) as $name) {
            echo "  {$name}\n";
        }
    }
}
