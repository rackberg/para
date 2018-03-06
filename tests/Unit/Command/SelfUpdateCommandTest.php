<?php

namespace Para\Tests\Unit\Command;

use Para\Command\SelfUpdateCommand;
use Para\Factory\ProcessFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;

/**
 * Class SelfUpdateCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class SelfUpdateCommandTest extends TestCase
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
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->processFactory = $this->prophesize(ProcessFactoryInterface::class);

        $this->application = new Application();
        $this->application->add(new SelfUpdateCommand(
            $this->logger->reveal(),
            $this->application,
            $this->processFactory->reveal(),
            'path/to/tools'
        ));
    }

    /**
     * Tests that the execute() method returns the output of the successful update process.
     */
    public function testTheExecuteMethodReturnsTheOutputOfTheSuccessfulUpdateProcess()
    {
        $command = $this->application->find('self-update');
        $parameters = [
            'command' => $command->getName(),
        ];

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->getOutput()->willReturn('foo');
        $process->getErrorOutput()->willReturn('');

        $this->processFactory
            ->getProcess('./update.sh stable', Argument::type('string'))
            ->willReturn($process->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->processFactory
            ->getProcess(Argument::any(), Argument::any())
            ->shouldHaveBeenCalled();

        $this->assertContains('foo', $output);
    }

    /**
     * Tests that the execute() method returns the error output of the failed update process.
     */
    public function testTheExecuteMethodReturnsTheErrorOutputOfTheFailedUpdateProcess()
    {
        $command = $this->application->find('self-update');
        $parameters = [
            'command' => $command->getName(),
        ];

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->getOutput()->willReturn('foo');
        $process->getErrorOutput()->willReturn('bar');

        $this->processFactory
            ->getProcess('./update.sh stable', Argument::type('string'))
            ->willReturn($process->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->processFactory
            ->getProcess(Argument::any(), Argument::any())
            ->shouldHaveBeenCalled();

        $this->assertContains('bar', $output);
    }
}
