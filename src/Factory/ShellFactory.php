<?php

namespace Para\Factory;

use Para\Service\GroupShell;
use Para\Service\HistoryShellManagerInterface;
use Para\Service\InteractiveShellInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ShellFactory.
 *
 * @package Para\Factory
 */
class ShellFactory implements ShellFactoryInterface
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
     * {@inheritdoc}
     */
    public function create(InputInterface $input, OutputInterface $output): InteractiveShellInterface
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
