<?php

namespace Para\Tests\Unit\Command;

use Para\Command\AddProjectCommand;
use Para\Configuration\GroupConfigurationInterface;
use Para\Decorator\EntityArrayDecoratorInterface;
use Para\Entity\GroupInterface;
use Para\Entity\ProjectInterface;
use Para\Factory\DecoratorFactoryInterface;
use Para\Factory\GroupFactoryInterface;
use Para\Factory\ProjectFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class AddProjectCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class AddProjectCommandTest extends TestCase
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
     * The project factory mock object.
     *
     * @var ProjectFactoryInterface
     */
    private $projectFactory;

    /**
     * The group factory mock object.
     *
     * @var GroupFactoryInterface
     */
    private $groupFactory;

    /**
     * The project array decorator factory mock object.
     *
     * @var DecoratorFactoryInterface
     */
    private $projectArrayDecoratorFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->groupConfiguration = $this->prophesize(GroupConfigurationInterface::class);
        $this->projectFactory = $this->prophesize(ProjectFactoryInterface::class);
        $this->groupFactory = $this->prophesize(GroupFactoryInterface::class);
        $this->projectArrayDecoratorFactory = $this->prophesize(DecoratorFactoryInterface::class);

        $this->application = new Application();
        $this->application->add(new AddProjectCommand(
            $this->logger->reveal(),
            $this->groupConfiguration->reveal(),
            $this->projectFactory->reveal(),
            $this->groupFactory->reveal(),
            $this->projectArrayDecoratorFactory->reveal()
        ));
    }

    /**
     * Tests that the execute() method returns the correct output when adding a project was successful.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenAddingAProjectWasSuccessful()
    {
        $command = $this->application->find('add:project');
        $parameters = $this->getCommandParameters();

        /** @var GroupInterface $group */
        $group = $this->prophesize(GroupInterface::class);

        $this->groupConfiguration
            ->getGroup('my_group')
            ->willReturn($group->reveal());

        $this->groupConfiguration->save()->shouldBeCalled();

        $project = $this->prophesize(ProjectInterface::class);

        $this->projectFactory
            ->getProject('my_project', 'path/to/project', Argument::type('string'), Argument::type('string'))
            ->willReturn($project->reveal());

        $projectArrayDecorator = $this->prophesize(EntityArrayDecoratorInterface::class);
        $projectArrayDecorator->asArray()
            ->willReturn([
                'name' => 'my_project',
                'path' => 'path/to/project',
                'foreground_color' => 13,
                'background_color' => 25,
            ]);

        $this->projectArrayDecoratorFactory
            ->getArrayDecorator($project->reveal())
            ->willReturn($projectArrayDecorator->reveal());

        $group->addProject(Argument::type('array'))->shouldBeCalled();

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('Successfully added the project "my_project" to the group "my_group"', $output);
    }

    /**
     * Tests that the execute() method writes a log message when adding a new project failed.
     */
    public function testTheExecuteMethodWritesALogMessageWhenAddingAProjectFailed()
    {
        $command = $this->application->find('add:project');
        $parameters = $this->getCommandParameters();

        $group = $this->prophesize(GroupInterface::class);
        $this->groupFactory->getGroup(Argument::type('string'))->willReturn($group->reveal());

        $this->logger
            ->error(Argument::type('string'), Argument::type('array'))
            ->shouldBeCalled();

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);
    }

    /**
     * Returns an array with command parameters.
     *
     * @return array
     */
    private function getCommandParameters(): array
    {
        return [
            'command' => 'add:project',
            'project_name' => 'my_project',
            'project_path' => 'path/to/project',
            'group_name' => 'my_group',
            '--foreground_color' => 13,
            '--background_color' => 25,
        ];
    }
}
