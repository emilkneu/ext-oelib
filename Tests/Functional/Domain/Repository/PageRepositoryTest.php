<?php

declare(strict_types=1);

namespace OliverKlee\Oelib\Tests\Functional\Domain\Repository;

use OliverKlee\Oelib\Domain\Repository\PageRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \OliverKlee\Oelib\Domain\Repository\PageRepository
 */
final class PageRepositoryTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/oelib'];

    /**
     * @var PageRepository
     */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new PageRepository();
        $this->importDataSet(__DIR__ . '/Fixtures/NestedPages.xml');
    }

    /**
     * @test
     */
    public function findWithinParentPagesForNegativeRecursionThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$recursion must be >= 0, but actually is: -1');
        $this->expectExceptionCode(1608389744);

        // @phpstan-ignore-next-line We are explicitly testing the contract violation here.
        $this->subject->findWithinParentPages([], -1);
    }

    /**
     * @test
     */
    public function findWithinParentPagesForEmptyArrayAndNoRecursionReturnsEmptyArray(): void
    {
        $result = $this->subject->findWithinParentPages([]);

        self::assertSame([], $result);
    }

    /**
     * @return array<int, array{0: int}>
     */
    public function recursionDataProvider(): array
    {
        return [
            0 => [0],
            1 => [1],
            2 => [2],
        ];
    }

    /**
     * @test
     *
     * @dataProvider recursionDataProvider
     */
    public function findWithinParentPagesForEmptyArrayAndAnyRecursionReturnsEmptyArray(int $recursion): void
    {
        // @phpstan-ignore-next-line We are explicitly testing the contract violation here.
        $result = $this->subject->findWithinParentPages([], $recursion);

        self::assertSame([], $result);
    }

    /**
     * @return array<string, array<int, array<int, positive-int>>>
     */
    public function pagesWithoutSubpagesDataProvider(): array
    {
        return [
            '1 existing page without subpages' => [[1]],
            '1 existing page with 1 deleted subpages' => [[9]],
            '2 existing pages without subpages' => [[1, 2]],
            '1 deleted page' => [[3]],
            '1 inexistent pages' => [[1000]],
            '2 inexistent pages' => [[1000, 1001]],
            'existing and inexistent pages' => [[1, 1000]],
        ];
    }

    /**
     * @return array<string, array<int, array<int, positive-int>>>
     */
    public function pagesWithDirectSubpagesDataProvider(): array
    {
        return [
            '1 existing page with 1 subpage' => [[4], [4, 5]],
            '1 existing page with 2 subpages' => [[6], [6, 7, 8]],
            '2 existing page with 3 subpages total' => [[4, 6], [4, 5, 6, 7, 8]],
        ];
    }

    /**
     * @test
     *
     * @param array<int, positive-int> $pageUids
     *
     * @dataProvider pagesWithoutSubpagesDataProvider
     * @dataProvider pagesWithDirectSubpagesDataProvider
     */
    public function findWithinParentPagesWithMissingRecursionReturnsOnlyTheProvidedPages(array $pageUids): void
    {
        $result = $this->subject->findWithinParentPages($pageUids);

        self::assertSame($pageUids, $result);
    }

    /**
     * @test
     *
     * @param array<int, positive-int> $pageUids
     *
     * @dataProvider pagesWithoutSubpagesDataProvider
     * @dataProvider pagesWithDirectSubpagesDataProvider
     */
    public function findWithinParentPagesWithZeroRecursionReturnsOnlyTheProvidedPages(array $pageUids): void
    {
        $result = $this->subject->findWithinParentPages($pageUids, 0);

        self::assertSame($pageUids, $result);
    }

    /**
     * @test
     *
     * @param array<int, positive-int> $parentUids
     * @param array<int, positive-int> $childUids
     *
     * @dataProvider pagesWithDirectSubpagesDataProvider
     */
    public function findWithinParentPagesWithRecursionFindsFindsDirectSubpage(array $parentUids, array $childUids): void
    {
        $result = $this->subject->findWithinParentPages($parentUids, 1);

        self::assertSame($childUids, $result);
    }

    /**
     * @test
     */
    public function findWithinParentPagesCanDoMultipleLevelsOfRecursion(): void
    {
        $result = $this->subject->findWithinParentPages([11], 2);

        self::assertSame([11, 12, 13], $result);
    }

    /**
     * @test
     */
    public function findWithinParentPagesIgnoresDeeperRecursionThanSpecified(): void
    {
        $result = $this->subject->findWithinParentPages([11], 1);

        self::assertSame([11, 12], $result);
    }

    /**
     * @test
     */
    public function findWithinParentPagesWithoutRecursionAndWithoutSubpagesSortsResult(): void
    {
        $result = $this->subject->findWithinParentPages([3, 2, 1]);

        self::assertSame([1, 2, 3], $result);
    }

    /**
     * @test
     */
    public function findWithinParentPagesWithSubpagesSortsResult(): void
    {
        $result = $this->subject->findWithinParentPages([11, 4], 2);

        self::assertSame([4, 5, 11, 12, 13], $result);
    }

    /**
     * @return array<string, array{0: string|int}>
     */
    public function invalidUidDataProvider(): array
    {
        return [
            'empty string' => [''],
            'non-empty string' => ['Bratwurst'],
            'zero' => [0],
            'negative int' => [-1],
        ];
    }

    /**
     * @test
     */
    public function findWithinParentPagesSilentlyCastsIntLikeUidStringsToInt(): void
    {
        // @phpstan-ignore-next-line We are explicitly testing with the contract violation here.
        $result = $this->subject->findWithinParentPages(['11'], 2);

        self::assertSame([11, 12, 13], $result);
    }

    /**
     * @test
     *
     * @param string|int $invalidUid
     *
     * @dataProvider invalidUidDataProvider
     */
    public function findWithinParentPagesWithSubpagesSilentlyDropsInvalidUids($invalidUid): void
    {
        // @phpstan-ignore-next-line We are explicitly testing with the contract violation here.
        $result = $this->subject->findWithinParentPages([1, $invalidUid]);

        self::assertSame([1], $result);
    }
}
