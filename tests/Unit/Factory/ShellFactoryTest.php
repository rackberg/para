<?php

namespace Para\Tests\Unit\Factory;

use Para\Factory\ProcessFactoryInterface;
use Para\Factory\ShellFactory;
use Para\Service\HistoryShellManagerInterface;
use Para\Service\InteractiveShellInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ShellFactoryTest
 *
 * @package Para\Tests\Unit\Factory
 */
class ShellFactoryTest extends TestCase
{
    /**
     * Tests that the create() method returns an instance of InteractiveShellInterface.
     */
    public function testTheCreateMethodReturnsAnInstanceOfInteractiveShellInterface()
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $application = $this->prophesize(Application::class);
        $processFactory = $this->prophesize(ProcessFactoryInterface::class);
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $historyShellManager = $this->prophesize(HistoryShellManagerInterface::class);

        $shellFactory = new ShellFactory(
            $logger->reveal(),
            $application->reveal(),
            $processFactory->reveal(),
            $dispatcher->reveal(),
            $historyShellManager->reveal()
        );

        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $shell = $shellFactory->create($input->reveal(), $output->reveal());

        $this->assertTrue($shell instanceof InteractiveShellInterface);
    }
}
