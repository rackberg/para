<?php
/**
 * @file
 * Contains lrackwitz\Para\Command\ShowLogCommand.php.
 */

namespace lrackwitz\Para\Command;

use lrackwitz\Para\Service\ConfigurationManagerInterface;
use lrackwitz\Para\Service\ProcessFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShowLogCommand.
 *
 * @package lrackwitz\Para\Command
 */
class ShowLogCommand extends Command
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The path to the log files.
     *
     * @var string
     */
    private $logPath;

    /**
     * The process factory.
     *
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * The configuration manager.
     *
     * @var ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * ShowLogCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \lrackwitz\Para\Service\ProcessFactory $processFactory The process factory.
     * @param \lrackwitz\Para\Service\ConfigurationManagerInterface $configManager The configuration manager.
     * @param string $logPath The path where the log files are saved.
     */
    public function __construct(
        LoggerInterface $logger,
        ProcessFactory $processFactory,
        ConfigurationManagerInterface $configManager,
        $logPath
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->processFactory = $processFactory;
        $this->configManager = $configManager;
        $this->logPath = $logPath;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('show:log')
            ->setDescription('Shows the log of the last command executed for a specific project.')
            ->setAliases(['log'])
            ->addArgument(
                'project',
                InputArgument::REQUIRED,
                'The name of the project to show the log for.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');
        if (!$this->configManager->hasProject($project)) {
            $this->logger->warning('The user tries to show the log for an unknown project.', [
                'arguments' => $input->getArguments()
            ]);
            $output->writeln(
                sprintf(
                    '<error>The project "%s" is not configured.',
                    $project
                ),
                1
            );
            return false;
        }

        $logFile = $this->logPath . strtolower($project) . '.log';

        // Check if the log file exists.
        if (!file_exists($logFile)) {
            $this->logger->error('The log file to show does not exist.', [
                'arguments' => $input->getArguments(),
                'logFile' => $logFile,
            ]);
            $output->writeln(
                sprintf(
                    '<error>The log file for the project "%s" could not be found.</error>',
                    $project
                ),
                1
            );
            return false;
        }

        // Create a new process.
        $process = $this->processFactory->create('cat ' . $logFile);
        $process->run();
        $output->write($process->getOutput());
    }
}
