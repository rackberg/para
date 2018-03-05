<?php

namespace Para\Tests\Unit\Configuration;

use Para\Configuration\GroupConfiguration;
use Para\Dumper\DumperInterface;
use Para\Entity\Group;
use Para\Entity\GroupInterface;
use Para\Factory\GroupFactoryInterface;
use Para\Service\ConfigurationManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * Class GroupConfigurationTest
 *
 * @package Para\Tests\Unit\Configuration
 */
class GroupConfigurationTest extends TestCase
{
    /**
     * The group configuration to test.
     *
     * @var \Para\Configuration\GroupConfigurationInterface
     */
    private $groupConfiguration;

    /**
     * The group factory mock object.
     *
     * @var \Para\Factory\GroupFactoryInterface
     */
    private $groupFactory;

    /**
     * The configuration manager mock object.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $defaultGroup = new Group();
        $defaultGroup->setName('default');

        $this->groupFactory = $this->prophesize(GroupFactoryInterface::class);
        $this->groupFactory
            ->getGroup('default')
            ->willReturn($defaultGroup);

        $this->configurationManager = $this->prophesize(ConfigurationManagerInterface::class);

        $this->groupConfiguration = new GroupConfiguration(
            $this->configurationManager->reveal(),
            $this->groupFactory->reveal()
        );
    }

    /**
     * Tests that the addGroup() method adds a new group entry into the
     */
    public function testTheAddGroupMethodAddsANewGroupIntoTheConfigurationFile()
    {
        $group = new Group();
        $group->setName('new_group');
        $this->groupConfiguration->addGroup($group);

        $groups = $this->groupConfiguration->getGroups();

        $this->assertArrayHasKey($group->getName(), $groups);
    }

    /**
     * Tests that the addGroup() method throws an exception when the group to add already exists.
     *
     * @expectedException \Para\Exception\AddGroupException
     * @expectedExceptionMessage The group to add is already configured
     */
    public function testTheAddGroupMethodThrowsExceptionWhenTheGroupToAddAlreadyExists()
    {
        $group = new Group();
        $group->setName('default');
        $this->groupConfiguration->addGroup($group);
    }

    /**
     * Tests that the deleteGroup() method deletes an existing group from the configuration file.
     */
    public function testTheDeleteGroupMethodDeletesAnExistingGroupFromTheConfigurationFile()
    {
        $this->groupConfiguration->deleteGroup('default');

        $this->assertEmpty($this->groupConfiguration->getGroups());
    }

    /**
     * Tests that a GroupNotFoundException will be thrown when trying to delete a not existing group.
     *
     * @expectedException \Para\Exception\GroupNotFoundException
     * @expectedExceptionMessage The group "not_existing_group" is not configured and
     *      could not be deleted from the configuration
     */
    public function testTheDeleteGroupMethodThrowsAGroupNotFoundExceptionWhenTheGroupToDeleteDoesNotExist()
    {
        $this->groupConfiguration->deleteGroup('not_existing_group');
    }

    /**
     * Tests that the getGroup() method returns a group instance for a configured group.
     */
    public function testTheGetGroupMethodReturnsAGroupInstanceForAConfiguredGroup()
    {
        $group = $this->groupConfiguration->getGroup('default');

        $this->assertTrue($group instanceof GroupInterface);
    }

    /**
     * Tests that the save() method saves the groups int the configuration file.
     */
    public function testSaveTheGroupsIntoTheConfigurationFile()
    {
        /** @var DumperInterface $dumper */
        $dumper = $this->prophesize(DumperInterface::class);
        $dumper->dump(Argument::type('array'))->shouldBeCalled();

        $this->configurationManager->getDumper()->willReturn($dumper->reveal());
        $this->configurationManager
            ->save(Argument::type('string'))
            ->shouldBeCalled();

        $configuration = [
            'default' => [],
        ];
        $this->groupConfiguration->save($configuration);
    }
}
