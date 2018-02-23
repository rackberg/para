<?php

namespace Para\Service;

use Para\Factory\ProcessFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ShellFactory.
 *
 * @package Para\Service
 */
class ShellFactory
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The application.
     *
     * @var Application
     */
    private $application;

    /**
     * The process factory.
     *
     * @var \Para\Factory\ProcessFactoryInterface
     */
    private $processFactory;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * The history shell manager.
     *
     * @var HistoryShellManagerInterface
     */
    private $historyShellManager;

    /**
     * ShellFactory constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Symfony\Component\Console\Application $application The application.
     * @param \Para\Factory\ProcessFactoryInterface $processFactory The process factory.
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher The event dispatcher.
     * @param \Para\Service\HistoryShellManagerInterface $historyShellManager The history shell manager.
     */
    public function __construct(
        LoggerInterface $logger,
        Application $application,
        ProcessFactoryInterface $processFactory,
        EventDispatcherInterface $dispatcher,
        HistoryShellManagerInterface $historyShellManager
    ) {
        $this->logger = $logger;
        $this->application = $application;
        $this->processFactory = $processFactory;
        $this->dispatcher = $dispatcher;
        $this->historyShellManager = $historyShellManager;
    }

    /**
     * Creates a new shell factory.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The console input.
     * @param \Symfony\Component\Console\Output\OutputInterface $output The console output.
     *
     * @return \Para\Service\GroupShell The created shell.
     */
    public function create(InputInterface $input, OutputInterface $output)
    {
        return new GroupShell(
            $this->logger,
            $this->application,
            $this->processFactory,
            $this->dispatcher,
            $this->historyShellManager,
            $input,
            $output
        );
    }
}
