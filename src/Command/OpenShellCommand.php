<?php
/**
 * @file
 * Contains Para\Command\OpenShellCommand.php.
 */

namespace Para\Command;

use Para\Factory\ShellFactoryInterface;
use Para\Service\ConfigurationManagerInterface;
use Para\Factory\ShellFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OpenShellCommand.
 *
 * @package Para\Command
 */
class OpenShellCommand extends Command
{
    /**
     * The logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * The shell factory.
     *
     * @var \Para\Factory\ShellFactoryInterface
     */
    private $shellFactory;

    /**
     * The configuration manager.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * The path to the history file.
     *
     * @var string
     */
    private $historyFile;

    /**
     * OpenShellCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Para\Factory\ShellFactoryInterface $shellFactory The shell factory.
     * @param \Para\Service\ConfigurationManagerInterface $configManager The configuration manager.
     * @param string $historyFile The path to the history file.
     */
    public function __construct(
        LoggerInterface $logger,
        ShellFactoryInterface $shellFactory,
        ConfigurationManagerInterface $configManager,
        $historyFile
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->shellFactory = $shellFactory;
        $this->configManager = $configManager;
        $this->historyFile = $historyFile;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('open:shell')
            ->setAliases(['shell'])
            ->setDescription('Opens a new interactive shell for a project group.')
            ->addArgument(
                'group',
                InputArgument::REQUIRED,
                'The name of the group.'
            )
            ->addOption(
                'exclude-project',
                'x',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'This excludes the projects that will not be affected by execution of commands in the shell.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Check if the group the user wants to use exists.
        $group = $input->getArgument('group');
        if (!$this->configManager->hasGroup($group)) {
            $this->logger->warning('The group the user tries use is not configured.', [
                'arguments' => $input->getArguments(),
                'options' => $input->getOptions(),
            ]);

            $output->writeln('<error>The group you are trying to use is not configured.</error>', 1);
            return false;
        }

        $excludedProjects = $input->getOption('exclude-project');

        $shell = $this->shellFactory->create($input, $output);

        $shell->run($group, $excludedProjects, $this->historyFile);

        // Persist the shell commands to the history file.
        if ($this->historyFile) {
            $shell
                ->getHistoryShellManager()
                ->getHistory()
                ->saveHistory($this->historyFile);
        }

        $output->writeln('<info>Finished para shell.</info>');
    }
}
