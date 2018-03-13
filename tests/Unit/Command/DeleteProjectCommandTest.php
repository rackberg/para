<?php

namespace Para\Tests\Unit\Command;

use Para\Command\DeleteProjectCommand;
use Para\Configuration\GroupConfigurationInterface;
use Para\Entity\Group;
use Para\Entity\Project;
use Para\Exception\ProjectNotFoundException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class DeleteProjectCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class DeleteProjectCommandTest extends TestCase
{
    /**
     * The application.
     *
     * @var \Symfony\Component\Console\Application
     */
    private $application;

    /**
     * The logger mock object.
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * The group configuration mock object.
     *
     * @var GroupConfigurationInterface
     */
    private $groupConfiguration;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->groupConfiguration = $this->prophesize(GroupConfigurationInterface::class);

        $this->application = new Application();
        $this->application->add(new DeleteProjectCommand(
            $this->logger->reveal(),
            $this->groupConfiguration->reveal(),
            'the/path/to/the/config/file.yml'
        ));
    }

    /**
     * Tests that the execute() method returns the correct output when a project has been deleted.
     */
    public function testTheMethodExecuteReturnsTheCorrectOutputWhenAProjectHasBeenDeleted()
    {
        $command = $this->application->find('delete:project');
        $parameters = [
            'command' => $command->getName(),
            'project_name' => 'my_project',
        ];

        $group1 = new Group();
        $group1->setName('default');
        $group1->addProject(['name' => 'project1', 'path' => '']);
        $group1->addProject(['name' => 'my_project', 'path' => '']);
        $group1->addProject(['name' => 'project2', 'path' => '']);

        $group2 = new Group();
        $group2->setName('second_group');
        $group2->addProject(['name' => 'project3', 'path' => '']);
        $group2->addProject(['name' => 'project4', 'path' => '']);

        $this->groupConfiguration
            ->load(Argument::type('string'))
            ->shouldBeCalled();

        $this->groupConfiguration
            ->getGroups()
            ->willReturn([$group1, $group2]);

        $this->groupConfiguration
            ->removeProject('my_project')
            ->shouldBeCalled();

        $this->groupConfiguration
            ->save(Argument::type('string'))
            ->shouldBeCalled();

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('Successfully deleted the project from the configuration.', $output);
    }

    /**
     * Tests that the execute() method returns the correct output when the project to delete is not configured.
     */
    public function testTheMethodExecuteReturnsTheCorrectOutputWhenTheProjectToDeleteIsNotConfigured()
    {
        $command = $this->application->find('delete:project');
        $parameters = [
            'command' => $command->getName(),
            'project_name' => 'unknown_project',
        ];

        $this->groupConfiguration
            ->load(Argument::type('string'))
            ->shouldBeCalled();

        $this->groupConfiguration
            ->removeProject('unknown_project')
            ->willThrow(new ProjectNotFoundException('unknown_project'));

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains(
            'The project you are trying to delete is '.
            'not stored in the configuration.',
            $output
        );
    }
}
