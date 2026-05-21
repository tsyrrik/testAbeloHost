<?php
declare(strict_types=1);

namespace App\Console\Command;

use App\Database\Seeders\ArticleSeeder;
use App\Database\Seeders\CategorySeeder;
use Throwable;

final class SeedCommand extends Command
{
    public function run(array $args): int
    {
        $pdo = $this->container->pdo();
        $fresh = in_array('--fresh', $args, true);

        if ($fresh) {
            echo "Truncating tables...\n";
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $pdo->exec('TRUNCATE TABLE article_categories');
            $pdo->exec('TRUNCATE TABLE articles');
            $pdo->exec('TRUNCATE TABLE categories');
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        }

        $pdo->beginTransaction();
        try {
            $categoryMap = (new CategorySeeder($pdo))->run();
            echo 'Seeded ' . count($categoryMap) . " categories.\n";

            $articleCount = (new ArticleSeeder($pdo))->run($categoryMap);
            echo "Seeded {$articleCount} articles.\n";

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        return 0;
    }
}
