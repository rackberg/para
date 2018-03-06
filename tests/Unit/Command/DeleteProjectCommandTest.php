<?php

namespace Para\Tests\Unit\Command;

use Para\Command\DeleteProjectCommand;
use Para\Exception\ProjectNotFoundException;
use Para\Service\ConfigurationManagerInterface;
use PHPUnit\Framework\TestCase;
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
        $this->application->add(new DeleteProjectCommand(
            $this->logger->reveal(),
            $this->configManager->reveal()
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

        $this->configManager
            ->deleteProject('my_project')
            ->willReturn(true);

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

        $this->configManager
            ->deleteProject('unknown_project')
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
