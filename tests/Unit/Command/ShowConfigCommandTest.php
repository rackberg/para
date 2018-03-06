<?php

namespace Para\Tests\Unit\Command;

use Para\Command\ShowConfigCommand;
use Para\Factory\ProcessFactoryInterface;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;

/**
 * Class ShowConfigCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class ShowConfigCommandTest extends TestCase
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
        $this->application->add(new ShowConfigCommand(
            $this->logger->reveal(),
            $this->processFactory->reveal(),
            'the/config/path'
        ));
    }

    /**
     * Tests that the execute() method returns the correct output when the command is successful.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheCommandIsSuccessful()
    {
        $command = $this->application->find('show:config');
        $parameters = [
            'command' => $command->getName(),
        ];

        $prophet = new PHPProphet();
        $prophecy = $prophet->prophesize('\Para\Command');
        $prophecy->file_exists(Argument::type('string'))->willReturn(true);
        $prophecy->reveal();

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->getOutput()->willReturn('foo');

        $this->processFactory
            ->getProcess(Argument::type('string'))
            ->willReturn($process->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('foo', $output);

        $prophet->checkPredictions();
    }

    /**
     * Tests that the execute() method returns the correct output when the config file does not exist.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheConfigFileDoesNotExist()
    {
        $command = $this->application->find('show:config');
        $parameters = [
            'command' => $command->getName(),
        ];

        $prophet = new PHPProphet();
        $prophecy = $prophet->prophesize('\Para\Command');
        $prophecy->file_exists(Argument::type('string'))->willReturn(false);
        $prophecy->reveal();

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The config file could not be found', $output);

        $prophet->checkPredictions();
    }
}
