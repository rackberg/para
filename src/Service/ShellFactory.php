<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\ShellFactory.php.
 */

namespace lrackwitz\Para\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShellFactory.
 *
 * @package lrackwitz\Para\Service
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
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * ShellFactory constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Symfony\Component\Console\Application $application The application.
     * @param \lrackwitz\Para\Service\ProcessFactory $processFactory The process factory.
     */
    public function __construct(
        LoggerInterface $logger,
        Application $application,
        ProcessFactory $processFactory
    ) {
        $this->logger = $logger;
        $this->application = $application;
        $this->processFactory = $processFactory;
    }

    /**
     * Creates a new shell factory.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The console input.
     * @param \Symfony\Component\Console\Output\OutputInterface $output The console output.
     *
     * @return \lrackwitz\Para\Service\GroupShell The created shell.
     */
    public function create(InputInterface $input, OutputInterface $output)
    {
        return new GroupShell(
            $this->logger,
            $this->application,
            $this->processFactory,
            $input,
            $output
        );
    }
}
