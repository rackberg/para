<?php

namespace Para\Tests\Unit\Command;

use Para\Command\ShowLogCommand;
use Para\Configuration\GroupConfigurationInterface;
use Para\Entity\Project;
use Para\Factory\ProcessFactoryInterface;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;

/**
 * Class ShowLogCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class ShowLogCommandTest extends TestCase
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
     * The process factory mock object.
     *
     * @var \Para\Factory\ProcessFactoryInterface
     */
    private $processFactory;

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
        $this->processFactory = $this->prophesize(ProcessFactoryInterface::class);
        $this->groupConfiguration = $this->prophesize(GroupConfigurationInterface::class);

        $this->application = new Application();
        $this->application->add(new ShowLogCommand(
            $this->logger->reveal(),
            $this->processFactory->reveal(),
            $this->groupConfiguration->reveal(),
            'the/log/path/'
        ));
    }

    /**
     * Tests that the execute() method returns the correct output when the project requested is not configured.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheProjectRequestedIsNotConfigured()
    {
        $command = $this->application->find('show:log');
        $parameters = [
            'command' => $command->getName(),
            'project' => 'my_project',
        ];

        $this->groupConfiguration
            ->getProject('my_project')
            ->willReturn(null);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The project "my_project" is not configured', $output);
    }

    /**
     * Tests that the execute() method returns the correct output when the project is configured and the log file exists.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheProjectIsConfiguredAndTheLogFileExists()
    {
        $processOutput = 'The process output';

        $command = $this->application->find('show:log');
        $parameters = [
            'command' => $command->getName(),
            'project' => 'my_project',
        ];

        $project = new Project('my_project', '');

        $this->groupConfiguration
            ->getProject('my_project')
            ->willReturn($project);

        $prophet = new PHPProphet();
        $prophecy = $prophet->prophesize('\Para\Command');
        $prophecy->file_exists(Argument::type('string'))->willReturn(true);
        $prophecy->reveal();

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->getOutput()->willReturn($processOutput);

        $this->processFactory
            ->getProcess('cat the/log/path/my_project.project.log')
            ->willReturn($process->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains($processOutput, $output);

        $prophet->checkPredictions();
    }

    /**
     * Tests that the execute() method returns the correct output when the project is configured but the log file does not exist.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheProjectIsConfiguredButTheLogFileDoesNotExist()
    {
        $command = $this->application->find('show:log');
        $parameters = [
            'command' => $command->getName(),
            'project' => 'my_project',
        ];

        $this->groupConfiguration
            ->getProject('my_project')
            ->willReturn(new Project('my_project', ''));

        $prophet = new PHPProphet();
        $prophecy = $prophet->prophesize('\Para\Command');
        $prophecy->file_exists(Argument::type('string'))->willReturn(false);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The log file for the project "my_project" could not be found', $output);
    }
}
