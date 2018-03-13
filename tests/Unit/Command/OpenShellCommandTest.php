<?php

namespace Para\Tests\Unit\Command;

use Para\Command\OpenShellCommand;
use Para\Configuration\GroupConfigurationInterface;
use Para\Entity\GroupInterface;
use Para\Factory\ShellFactoryInterface;
use Para\Service\GroupShell;
use Para\Service\HistoryShellManagerInterface;
use Para\Service\ShellHistoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class OpenShellCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class OpenShellCommandTest extends TestCase
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
     * The shell factory mock object.
     *
     * @var \Para\Factory\ShellFactoryInterface
     */
    private $shellFactory;

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
        $this->shellFactory = $this->prophesize(ShellFactoryInterface::class);
        $this->groupConfiguration = $this->prophesize(GroupConfigurationInterface::class);

        $this->application = new Application();
        $this->application->add(new OpenShellCommand(
            $this->logger->reveal(),
            $this->shellFactory->reveal(),
            $this->groupConfiguration->reveal(),
            'history_file.txt'
        ));
    }

    /**
     * Tests that the execute() method returns the correct output after the shell finished.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputAfterTheShellFinished()
    {
        $command = $this->application->find('open:shell');
        $parameters = [
            'command' => $command->getName(),
            'group' => 'my_group',
        ];

        $group = $this->prophesize(GroupInterface::class);

        $this->groupConfiguration
            ->getGroup('my_group')
            ->willReturn($group->reveal());

        $history = $this->prophesize(ShellHistoryInterface::class);
        $history->saveHistory(Argument::type('string'))->shouldBeCalled();

        $historyShellManager = $this->prophesize(HistoryShellManagerInterface::class);
        $historyShellManager->getHistory()->willReturn($history->reveal());

        $shell = $this->prophesize(GroupShell::class);
        $shell
            ->run('my_group', Argument::type('array'), Argument::type('string'))
            ->shouldBeCalled();
        $shell
            ->getHistoryShellManager()
            ->willReturn($historyShellManager->reveal());

        $this->shellFactory
            ->create(Argument::any(), Argument::any())
            ->willReturn($shell->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('Finished para shell', $output);
    }

    /**
     * Tests that the execute() method returns the correct output when the requested group is not configured.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheRequestedGroupIsNotConfigured()
    {
        $command = $this->application->find('open:shell');
        $parameters = [
            'command' => $command->getName(),
            'group' => 'my_group',
        ];

        $this->groupConfiguration
            ->getGroup('my_group')
            ->willReturn(null);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The group you are trying to use is not configured', $output);
    }
}
