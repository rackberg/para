<?php
/**
 * @file
 * Contains Para\Command\ExecuteCommand.php.
 */

namespace Para\Command;

use Para\Configuration\GroupConfigurationInterface;
use Para\Factory\BufferedOutputAdapterFactoryInterface;
use Para\Service\AsyncShellCommandExecutor;
use Para\Service\ConfigurationManagerInterface;
use Para\Service\Output\BufferedOutputAdapter;
use Para\Service\Output\BufferedOutputInterface;
use Para\Service\OutputBuffer\OutputBuffer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExecuteCommand.
 *
 * @package Para\Command
 */
class ExecuteCommand extends Command
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The asynchronous shell command executor.
     *
     * @var AsyncShellCommandExecutor
     */
    private $asyncExecutor;

    /**
     * The group configuration.
     *
     * @var GroupConfigurationInterface
     */
    private $groupConfiguration;

    /**
     * The buffered output adapter factory.
     *
     * @var \Para\Factory\BufferedOutputAdapterFactoryInterface
     */
    private $bufferedOutputAdapterFactory;

    /**
     * The path to the config file.
     *
     * @var string
     */
    private $configFile;

    /**
     * ExecuteCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Para\Service\AsyncShellCommandExecutor $asyncExecutor The asynchronous process executor.
     * @param GroupConfigurationInterface $groupConfiguration The group configuration.
     * @param \Para\Factory\BufferedOutputAdapterFactoryInterface $bufferedOutputAdapterFactory The buffered output adapter factory.
     * @param string $configFile The path to the config file.
     */
    public function __construct(
        LoggerInterface $logger = null,
        AsyncShellCommandExecutor $asyncExecutor,
        GroupConfigurationInterface $groupConfiguration,
        BufferedOutputAdapterFactoryInterface $bufferedOutputAdapterFactory,
        string $configFile
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->asyncExecutor = $asyncExecutor;
        $this->groupConfiguration = $groupConfiguration;
        $this->bufferedOutputAdapterFactory = $bufferedOutputAdapterFactory;
        $this->configFile = $configFile;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('execute')
            ->setDescription('Executes the shell command the user provides.')

            ->addArgument('group', InputArgument::REQUIRED)
            ->addArgument('cmd', InputArgument::REQUIRED)
            ->addOption(
                'exclude-project',
                'x',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'This excludes the projects that will not be affected by execution of the shell command.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Load the configuration.
        $this->groupConfiguration->load($this->configFile);

        // Make sure we are dealing with a buffered output.
        $outputAdapter = $this->bufferedOutputAdapterFactory->getOutputAdapter($output);

        // Get the shell command.
        $cmd = $input->getArgument('cmd');

        // Check if the group the user wants to use exists.
        $groupName = $input->getArgument('group');

        $group = $this->groupConfiguration->getGroup($groupName);
        if (!$group) {
            $this->logger->warning('The group the user tries use is not configured.', [
                'group' => $groupName,
                'cmd' => $cmd,
            ]);

            $output->writeln('<error>The group you are trying to use is not configured.</error>', 1);
            return false;
        }

        // Get all for the group available projects.
        $projects = $group->getProjects();
        if ($projects == []) {
            $output->writeln('<error>No projects found in the group "' . $groupName . '". Aborting execution.</error>', 1);
        }

        // Exclude one or more projects.
        if ($excludeProjects = $input->getOption('exclude-project')) {
            foreach ($excludeProjects as $project) {
                if (array_key_exists($project, $projects)) {
                    $this->logger->debug('User excludes project from execution.', [
                        'excludedProject' => $project,
                        'projects' => $projects,
                        'group' => $groupName,
                        'cmd' => $cmd,
                    ]);

                    unset($projects[$project]);
                }
            }
        }

        // Execute the shell command.
        $this->asyncExecutor->execute($cmd, $projects, $outputAdapter);
    }
}
