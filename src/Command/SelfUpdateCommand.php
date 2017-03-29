<?php
/**
 * @file
 * Contains lrackwitz\Para\Command\SelfUpdateCommand.php.
 */

namespace lrackwitz\Para\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class SelfUpdateCommand.
 *
 * @package lrackwitz\Para\Command
 */
class SelfUpdateCommand extends Command
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The application
     *
     * @var Application
     */
    private $application;

    /**
     * SelfUpdateCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Symfony\Component\Console\Application $application The application.
     */
    public function __construct(
        LoggerInterface $logger,
        Application $application
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->application = $application;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Checks for a new version of para and updates itself.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->debug('Self-Update executed by user.');

        $process = new Process('sh "tools/update.sh"');
        $process->run();

        $out = $process->getOutput();
        if ($process->getErrorOutput() != '') {
            $out = $process->getErrorOutput();
        }

        $output->write($out);
    }


}
