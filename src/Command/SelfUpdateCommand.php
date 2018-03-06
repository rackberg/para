<?php
/**
 * @file
 * Contains Para\Command\SelfUpdateCommand.php.
 */

namespace Para\Command;

use Para\Factory\ProcessFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class SelfUpdateCommand.
 *
 * @package Para\Command
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
     * The path to the tool scripts.
     *
     * @var string
     */
    private $toolsPath;

    /**
     * The process factory.
     *
     * @var \Para\Factory\ProcessFactoryInterface
     */
    private $processFactory;

    /**
     * SelfUpdateCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Symfony\Component\Console\Application $application The application.
     * @param \Para\Factory\ProcessFactoryInterface $processFactory The process factory.
     * @param string $toolsPath The path to the tool scripts.
     */
    public function __construct(
        LoggerInterface $logger,
        Application $application,
        ProcessFactoryInterface $processFactory,
        $toolsPath
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->application = $application;
        $this->processFactory = $processFactory;
        $this->toolsPath = $toolsPath;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Checks for a new version of para and updates itself.')
            ->addOption(
                'unstable',
                null,
                InputOption::VALUE_NONE,
                'Uses the latest commit for updating, which can be unstable instead of the latest stable release.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->debug('Self-Update executed by user.');

        // Get the update method.
        $updateMethod = $input->getOption('unstable') ? 'unstable' : 'stable';

        $process = $this->processFactory->getProcess('./update.sh ' . $updateMethod, $this->toolsPath);
        $process->run();

        $out = $process->getOutput();
        if ($process->getErrorOutput() != '') {
            $out = $process->getErrorOutput();
        }

        $output->write($out);
    }
}
