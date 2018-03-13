<?php

namespace Para\Tests\Unit\Configuration;

use org\bovigo\vfs\vfsStream;
use Para\Configuration\GroupConfiguration;
use Para\Dumper\DumperInterface;
use Para\Entity\Group;
use Para\Entity\GroupInterface;
use Para\Entity\Project;
use Para\Entity\ProjectInterface;
use Para\Factory\GroupFactoryInterface;
use Para\Factory\ProjectFactoryInterface;
use Para\Parser\ParserInterface;
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
     * The project factory mock object.
     *
     * @var ProjectFactoryInterface
     */
    private $projectFactory;

    /**
     * The parser mock object.
     *
     * @var ParserInterface
     */
    private $parser;

    /**
     * The dumper mock object.
     *
     * @var DumperInterface
     */
    private $dumper;

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

        $this->projectFactory = $this->prophesize(ProjectFactoryInterface::class);

        $this->parser = $this->prophesize(ParserInterface::class);
        $this->dumper = $this->prophesize(DumperInterface::class);

        $this->groupConfiguration = new GroupConfiguration(
            $this->parser->reveal(),
            $this->dumper->reveal(),
            $this->groupFactory->reveal(),
            $this->projectFactory->reveal(),
            'the/path/to/the/config/file.yml'
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
        $group = new Group('default');
        $this->groupConfiguration->addGroup($group);
        $this->groupConfiguration->addGroup($group);
    }

    /**
     * Tests that the deleteGroup() method deletes an existing group from the configuration file.
     */
    public function testTheDeleteGroupMethodDeletesAnExistingGroupFromTheConfigurationFile()
    {
        $this->groupConfiguration->addGroup(new Group('default'));
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
        $this->groupConfiguration->addGroup(new Group('default'));
        $group = $this->groupConfiguration->getGroup('default');

        $this->assertTrue($group instanceof GroupInterface);
    }

    /**
     * Tests that the save() method saves the groups int the configuration file.
     */
    public function testSaveTheGroupsIntoTheConfigurationFile()
    {
        $configContent = <<< EOF
groups:
    default:
        project1:
            path: "the/path/to/the/project"
        project2:
            path: "the/path/to/the/project"
        project3:
            path: "the/path/to/the/project"
        project4:
            path: "the/path/to/the/project"
EOF;

        $fileSystem = vfsStream::setup('root', null, [
            'config' => [
                'para.yml' => $configContent,
            ],
        ]);

        $this->parser
            ->parse($configContent)
            ->willReturn([
                'groups' => [
                    'default' => [
                        'project1' => [
                            'path' => 'the/path/to/the/project',
                        ],
                        'project2' => [
                            'path' => 'the/path/to/the/project',
                        ],
                        'project3' => [
                            'path' => 'the/path/to/the/project',
                        ],
                        'project4' => [
                            'path' => 'the/path/to/the/project',
                        ],
                    ],
                ],
            ]);

        $this->groupConfiguration->setConfigFile(vfsStream::url('root/config/para.yml'));

        $this->groupConfiguration->load();

        $newProject = new Project('my_new_project', 'this/is/the/path');

        $groupToAdd = new Group();
        $groupToAdd->setName('new_group');
        $groupToAdd->setProjects([
            $newProject
        ]);

        $this->groupConfiguration->addGroup($groupToAdd);

        $expectedContent = <<< EOF
default:
    project1:
        path: "the/path/to/the/project"
    project2:
        path: "the/path/to/the/project"
    project3:
        path: "the/path/to/the/project"
    project4:
        path: "the/path/to/the/project"
new_group:
    my_new_project:
        path: "this/is/the/path"
EOF;

        $this->dumper
            ->dump(Argument::type('array'))
            ->willReturn($expectedContent);

        $result = $this->groupConfiguration->save();

        $this->assertTrue($result);
        $this->assertEquals($expectedContent, $fileSystem->getChild('config/para.yml')->getContent());
    }

    /**
     * Tests that the removeProject() method removes a project.
     */
    public function testTheRemoveProjectMethodRemovesTheProjectFromTheGroup()
    {
        $projectName = 'my_project';
        $groupName = 'test';

        $group = new Group();
        $group->setName($groupName);
        $group->addProject(['name' => $projectName, 'path' => '']);

        $this->groupConfiguration->addGroup($group);

        $this->groupConfiguration->removeProject('my_project');

        $result = $this->groupConfiguration->getGroup($groupName);

        $this->assertEquals([], $result->getProjects());
    }

    /**
     * Tests that the getProject() method returns a project instance.
     */
    public function testTheGetProjectMethodReturnsAProjectInstance()
    {
        $projectData = [
            'my_project' => [
                'name' => 'my_project',
                'path' => 'the/path',
            ],
        ];
        $group = $this->prophesize(GroupInterface::class);
        $group
            ->getName()
            ->willReturn('default');
        $group
            ->getProjects()
            ->willReturn($projectData);
        $this->groupConfiguration->addGroup($group->reveal());

        $project = $this->prophesize(ProjectInterface::class);

        $this->projectFactory
            ->getProjectFromArray(Argument::type('array'))
            ->willReturn($project->reveal());

        $result = $this->groupConfiguration->getProject('my_project');

        $this->assertTrue($result instanceof ProjectInterface);
    }
}
