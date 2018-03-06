<?php

namespace Para\Tests\Unit\Command;

use Para\Command\ExecuteCommand;
use Para\Factory\BufferedOutputAdapterFactoryInterface;
use Para\Service\AsyncShellCommandExecutor;
use Para\Service\ConfigurationManagerInterface;
use Para\Service\Output\BufferedOutputInterface;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ExecuteCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class ExecuteCommandTest extends TestCase
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
     * The async shell executor mock object.
     *
     * @var \Para\Service\AsyncShellCommandExecutor
     */
    private $asyncExecutor;

    /**
     * The configuration manager mock object.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * The buffered output adapter factory mock object.
     *
     * @var \Para\Factory\BufferedOutputAdapterFactoryInterface
     */
    private $bufferedOutputAdapterFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->asyncExecutor = $this->prophesize(AsyncShellCommandExecutor::class);
        $this->configManager = $this->prophesize(ConfigurationManagerInterface::class);
        $this->bufferedOutputAdapterFactory = $this->prophesize(BufferedOutputAdapterFactoryInterface::class);

        $this->application = new Application();
        $this->application->add(new ExecuteCommand(
            $this->logger->reveal(),
            $this->asyncExecutor->reveal(),
            $this->configManager->reveal(),
            $this->bufferedOutputAdapterFactory->reveal()
        ));
    }

    /**
     * Tests that the execute() method returns the correct output when te group requested is not configured.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheGroupRequestedIsNotConfigured()
    {
        $command = $this->application->find('execute');
        $parameters = [
            'command' => $command->getName(),
            'group' => 'my_group',
            'cmd' => 'ls -la',
        ];

        $output = $this->prophesize(BufferedOutputInterface::class);

        $this->bufferedOutputAdapterFactory
            ->getOutputAdapter(Argument::type(OutputInterface::class))
            ->willReturn($output->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The group you are trying to use is not configured', $output);
    }

    /**
     * Tests that the execute() method returns the correct output when the group requested contains no projects.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheGroupRequestedContainsNoProjects()
    {
        $command = $this->application->find('execute');
        $parameters = [
            'command' => $command->getName(),
            'group' => 'my_group',
            'cmd' => 'pwd',
        ];

        $this->configManager
            ->hasGroup('my_group')
            ->willReturn(true);
        $this->configManager
            ->readGroup('my_group')
            ->willReturn([]);

        $output = $this->prophesize(BufferedOutputInterface::class);
        $this->bufferedOutputAdapterFactory
            ->getOutputAdapter(Argument::type(OutputInterface::class))
            ->willReturn($output->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('No projects found in the group "my_group". Aborting execution.', $output);
    }

    /**
     * Tests that the execute() method writes a log message for an excluded project.
     */
    public function testTheExecuteMethodWritesALogMessageForAnExcludedProject()
    {
        $command = $this->application->find('execute');
        $parameters = [
            'command' => $command->getName(),
            'group' => 'my_group',
            'cmd' => 'ls',
            '--exclude-project' => ['my_project'],
        ];

        $output = $this->prophesize(BufferedOutputInterface::class);
        $this->bufferedOutputAdapterFactory
            ->getOutputAdapter(Argument::type(OutputInterface::class))
            ->willReturn($output->reveal());

        $this->configManager
            ->hasGroup('my_group')
            ->willReturn(true);
        $this->configManager
            ->readGroup('my_group')
            ->willReturn(['my_project' => []]);

        $this->logger
            ->debug('User excludes project from execution.', Argument::type('array'))
            ->shouldBeCalledTimes(1);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);
    }
}
