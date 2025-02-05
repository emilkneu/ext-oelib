<?php

declare(strict_types=1);

namespace OliverKlee\Oelib\Tests\Functional\Mapper;

use OliverKlee\Oelib\Mapper\BackEndUserGroupMapper;
use OliverKlee\Oelib\Model\BackEndUserGroup;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \OliverKlee\Oelib\Mapper\BackEndUserGroupMapper
 * @covers \OliverKlee\Oelib\Model\BackEndUserGroup
 */
final class BackEndUserGroupMapperTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/oelib'];

    /**
     * @var BackEndUserGroupMapper
     */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new BackEndUserGroupMapper();

        $this->importDataSet(__DIR__ . '/../Fixtures/BackEndUsers.xml');
    }

    /**
     * @test
     */
    public function loadForExistingUserGroupCanLoadUserGroupData(): void
    {
        /** @var BackEndUserGroup $userGroup */
        $userGroup = $this->subject->find(1);
        $this->subject->load($userGroup);

        self::assertSame('The best!', $userGroup->getTitle());
    }

    /**
     * @test
     */
    public function subgroupRelationIsUserGroupList(): void
    {
        /** @var BackEndUserGroup $group */
        $group = $this->subject->find(1);
        self::assertInstanceOf(BackEndUserGroup::class, $group->getSubgroups()->first());
    }
}
