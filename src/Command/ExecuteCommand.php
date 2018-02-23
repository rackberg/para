<?php
/**
 * @file
 * Contains Para\Command\ExecuteCommand.php.
 */

namespace Para\Command;

use Para\Service\AsyncShellCommandExecutor;
use Para\Service\ConfigurationManagerInterface;
use Para\Service\Output\BufferedOutputAdapter;
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
     * The configuration manager.
     *
     * @var ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * ExecuteCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Para\Service\AsyncShellCommandExecutor $asyncExecutor The asynchronous process executor.
     * @param \Para\Service\ConfigurationManagerInterface $configManager The configuration manager.
     */
    public function __construct(
        LoggerInterface $logger = null,
        AsyncShellCommandExecutor $asyncExecutor,
        ConfigurationManagerInterface $configManager
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->asyncExecutor = $asyncExecutor;
        $this->configManager = $configManager;
    }

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Make sure we are dealing with a buffered output.
        $outputAdapter = new BufferedOutputAdapter($output);

        // Get the shell command.
        $cmd = $input->getArgument('cmd');

        // Check if the group the user wants to use exists.
        $group = $input->getArgument('group');
        if (!$this->configManager->hasGroup($group)) {
            $this->logger->warning('The group the user tries use is not configured.', [
                'group' => $group,
                'cmd' => $cmd,
            ]);

            $output->writeln('<error>The group you are trying to use is not configured.</error>', 1);
            return false;
        }

        // Get all for the group available projects.
        $projects = $this->configManager->readGroup($group);
        if ($projects == []) {
            $output->writeln('<error>No projects found in the group "' . $group . '". Aborting execution.</error>', 1);
        }

        // Exclude one or more projects.
        if ($excludeProjects = $input->getOption('exclude-project')) {
            foreach ($excludeProjects as $project) {
                if (array_key_exists($project, $projects)) {
                    $this->logger->debug('User excludes project from execution.', [
                        'excludedProject' => $project,
                        'projects' => $projects,
                        'group' => $group,
                        'cmd' => $cmd,
                    ]);
                    if ($output->isDebug()) {
                        $output->writeln(
                            'Ignoring configured project "'.$project.'" for command execution.'
                        );
                    }
                    unset($projects[$project]);
                }
            }
        }

        // Execute the shell command.
        $this->asyncExecutor->execute($cmd, $projects, $outputAdapter);
    }
}
