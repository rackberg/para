<?php

namespace Para\Tests\Unit\Command;

use Para\Command\AddProjectCommand;
use Para\Service\ConfigurationManagerInterface;
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
     * The configuration manager mock object.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->configManager = $this->prophesize(ConfigurationManagerInterface::class);

        $this->application = new Application();
        $this->application->add(new AddProjectCommand(
            $this->logger->reveal(),
            $this->configManager->reveal()
        ));
    }

    /**
     * Tests that the execute() method returns the correct output when adding a project was successful.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenAddingAProjectWasSuccessful()
    {
        $command = $this->application->find('add:project');
        $parameters = $this->getCommandParameters();

        $this->configManager
            ->addProject(
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                Argument::any(),
                Argument::any()
            )
            ->willReturn(true);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('Successfully added the project "my_project" to the group "my_group"', $output);
    }

    /**
     * Tests that the execute() method returns the correct response when adding a new project failed.
     */
    public function testTheExecuteMethodReturnsTheCorrectResponseWhenAddingAProjectFailed()
    {
        $command = $this->application->find('add:project');
        $parameters = $this->getCommandParameters();

        $this->configManager
            ->addProject(
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                Argument::any(),
                Argument::any()
            )
            ->willReturn(false);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('Failed to add the project', $output);
    }

    /**
     * Tests that the execute() method writes a log message when adding a new project failed.
     */
    public function testTheExecuteMethodWritesALogMessageWhenAddingAProjectFailed()
    {
        $command = $this->application->find('add:project');
        $parameters = $this->getCommandParameters();

        $this->configManager
            ->addProject(
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                Argument::any(),
                Argument::any()
            )
            ->willReturn(false);

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
