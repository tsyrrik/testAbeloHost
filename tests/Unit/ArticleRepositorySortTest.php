<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Repository\ArticleRepository;
use InvalidArgumentException;
use PDO;
use PHPUnit\Framework\TestCase;

final class ArticleRepositorySortTest extends TestCase
{
    public function testPaginateByCategoryRejectsUnknownSortKey(): void
    {
        $repo = new ArticleRepository(self::stubPdo());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown sort: alphabetical');

        $repo->paginateByCategory(1, 'alphabetical', 1, 6);
    }

    private static function stubPdo(): PDO
    {
        return new PDO('sqlite::memory:');
    }
}
