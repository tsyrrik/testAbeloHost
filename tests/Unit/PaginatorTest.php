<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Support\Paginator;
use PHPUnit\Framework\TestCase;

final class PaginatorTest extends TestCase
{
    public function testComputesPagesFromTotalAndPerPage(): void
    {
        $p = new Paginator(total: 25, perPage: 10, page: 1);

        self::assertSame(3, $p->pages);
        self::assertSame(1, $p->page);
    }

    public function testEmptyTotalStillExposesAtLeastOnePage(): void
    {
        $p = new Paginator(total: 0, perPage: 10, page: 1);

        self::assertSame(1, $p->pages);
        self::assertSame(1, $p->page);
    }

    public function testClampsRequestedPageBelowOneToOne(): void
    {
        $p = new Paginator(total: 50, perPage: 10, page: -5);

        self::assertSame(1, $p->page);
    }

    public function testClampsRequestedPageAboveLastToLast(): void
    {
        $p = new Paginator(total: 25, perPage: 10, page: 999);

        self::assertSame(3, $p->page);
    }

    public function testHasPrevAndNextFlagsForMiddlePage(): void
    {
        $a = (new Paginator(total: 50, perPage: 10, page: 3))->toArray();

        self::assertTrue($a['has_prev']);
        self::assertTrue($a['has_next']);
        self::assertSame([1, 2, 3, 4, 5], $a['range']);
    }

    public function testNoPrevOnFirstPageAndNoNextOnLastPage(): void
    {
        $first = (new Paginator(total: 50, perPage: 10, page: 1))->toArray();
        $last  = (new Paginator(total: 50, perPage: 10, page: 5))->toArray();

        self::assertFalse($first['has_prev']);
        self::assertTrue($first['has_next']);

        self::assertTrue($last['has_prev']);
        self::assertFalse($last['has_next']);
    }
}
