<?php
declare(strict_types=1);

namespace App\Console\Command;

use PDO;

final class MigrateCommand extends Command
{
    private const TABLE = 'migrations';

    public function run(array $args): int
    {
        $pdo = $this->container->pdo();
        $this->ensureTable($pdo);

        $applied = $this->fetchApplied($pdo);
        $files = $this->discoverFiles();

        $ran = 0;
        foreach ($files as $name => $path) {
            if (in_array($name, $applied, true)) {
                continue;
            }

            echo "Running {$name}... ";
            $sql = (string) file_get_contents($path);
            $pdo->exec($sql);

            $stmt = $pdo->prepare(
                'INSERT INTO ' . self::TABLE . ' (name, ran_at) VALUES (:name, NOW())'
            );
            $stmt->execute(['name' => $name]);

            echo "ok\n";
            $ran++;
        }

        echo $ran === 0
            ? "Nothing to migrate.\n"
            : "Applied {$ran} migration(s).\n";

        return 0;
    }

    private function ensureTable(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS ' . self::TABLE . ' (
                name   VARCHAR(255) NOT NULL,
                ran_at DATETIME NOT NULL,
                PRIMARY KEY (name)
            ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4'
        );
    }

    /** @return list<string> */
    private function fetchApplied(PDO $pdo): array
    {
        return $pdo->query('SELECT name FROM ' . self::TABLE)->fetchAll(PDO::FETCH_COLUMN);
    }

    /** @return array<string, string> filename → full path, sorted */
    private function discoverFiles(): array
    {
        $dir = $this->container->basePath() . '/src/Database/Migrations';
        $files = glob($dir . '/*.sql') ?: [];

        $map = [];
        foreach ($files as $path) {
            $map[basename($path)] = $path;
        }
        ksort($map);

        return $map;
    }
}
