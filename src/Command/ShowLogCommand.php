<?php

namespace Para\Command;

use Para\Configuration\GroupConfigurationInterface;
use Para\Exception\ProjectNotFoundException;
use Para\Factory\ProcessFactoryInterface;
use Para\Service\ConfigurationManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShowLogCommand.
 *
 * @package Para\Command
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
     * @var \Para\Factory\ProcessFactoryInterface
     */
    private $processFactory;

    /**
     * The group configuration.
     *
     * @var GroupConfigurationInterface
     */
    private $groupConfiguration;

    /**
     * ShowLogCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Para\Factory\ProcessFactoryInterface $processFactory The process factory.
     * @param GroupConfigurationInterface $groupConfiguration The group configuration.
     * @param string $logPath The path where the log files are saved.
     */
    public function __construct(
        LoggerInterface $logger,
        ProcessFactoryInterface $processFactory,
        GroupConfigurationInterface $groupConfiguration,
        string $logPath
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->processFactory = $processFactory;
        $this->groupConfiguration = $groupConfiguration;
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
        $projectName = $input->getArgument('project');

        $project = $this->groupConfiguration->getProject($projectName);
        if (!$project) {
            $this->logger->warning('The user tries to show the log for an unknown project.', [
                'arguments' => $input->getArguments()
            ]);
            $output->writeln(
                sprintf(
                    '<error>The project "%s" is not configured.',
                    $projectName
                ),
                1
            );
            return false;
        }

        $logFile = $this->logPath . strtolower($projectName) . '.project.log';

        // Check if the log file exists.
        if (!file_exists($logFile)) {
            $this->logger->error('The log file to show does not exist.', [
                'arguments' => $input->getArguments(),
                'logFile' => $logFile,
            ]);
            $output->writeln(
                sprintf(
                    '<error>The log file for the project "%s" could not be found.</error>',
                    $projectName
                ),
                1
            );
            return false;
        }

        // Create a new process.
        $process = $this->processFactory->getProcess('cat ' . $logFile);
        $process->run();
        $output->write($process->getOutput());
    }
}
