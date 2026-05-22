<?php
declare(strict_types=1);

namespace App\Tests\Integration;

use App\Repository\ArticleRepository;

final class ArticleRepositoryTest extends IntegrationTestCase
{
    private ArticleRepository $repo;

    protected function setUp(): void
    {
        $this->repo = new ArticleRepository(self::$pdo);
    }

    public function testPaginateByCategoryCountsAllArticlesInCategory(): void
    {
        $result = $this->repo->paginateByCategory(1, 'date', 1, 10);

        self::assertSame(4, $result['total']);
        self::assertCount(4, $result['items']);
    }

    public function testSortByDateReturnsNewestFirst(): void
    {
        $result = $this->repo->paginateByCategory(1, 'date', 1, 10);
        $slugs = array_column($result['items'], 'slug');

        self::assertSame(
            ['tech-travel-2', 'tech-travel', 'tech-b', 'tech-a'],
            $slugs,
        );
    }

    public function testSortByViewsReturnsMostViewedFirst(): void
    {
        $result = $this->repo->paginateByCategory(1, 'views', 1, 10);
        $slugs = array_column($result['items'], 'slug');

        self::assertSame(
            ['tech-b', 'tech-travel-2', 'tech-travel', 'tech-a'],
            $slugs,
        );
    }

    public function testPaginationSlicesResults(): void
    {
        $page1 = $this->repo->paginateByCategory(1, 'date', 1, 2);
        $page2 = $this->repo->paginateByCategory(1, 'date', 2, 2);

        self::assertSame(['tech-travel-2', 'tech-travel'], array_column($page1['items'], 'slug'));
        self::assertSame(['tech-b', 'tech-a'], array_column($page2['items'], 'slug'));
    }

    public function testOutOfRangePageIsClampedToLastPage(): void
    {
        $result = $this->repo->paginateByCategory(1, 'date', 999, 2);

        self::assertSame(2, $result['page']);
        self::assertSame(['tech-b', 'tech-a'], array_column($result['items'], 'slug'));
    }

    public function testFindRelatedRanksByMatchCountThenRecency(): void
    {
        $related = $this->repo->findRelated(articleId: 3, categoryIds: [1, 2], limit: 3);
        $slugs = array_column($related, 'slug');

        self::assertSame(['tech-travel-2', 'travel-a', 'tech-b'], $slugs);
    }

    public function testFindRelatedExcludesTargetArticle(): void
    {
        $related = $this->repo->findRelated(articleId: 3, categoryIds: [1, 2], limit: 10);

        foreach ($related as $r) {
            self::assertNotSame(3, $r['id']);
        }
    }

    public function testFindRelatedReturnsEmptyWhenNoCategories(): void
    {
        self::assertSame([], $this->repo->findRelated(articleId: 3, categoryIds: [], limit: 3));
    }
}
