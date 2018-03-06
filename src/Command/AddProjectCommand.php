<?php
/**
 * @file
 * Contains Para\Command\AddProjectCommand.php.
 */

namespace Para\Command;

use Para\Service\ConfigurationManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddProjectCommand.
 *
 * @package Para\Command
 */
class AddProjectCommand extends Command
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The configuration manager.
     *
     * @var ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * InitCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Para\Service\ConfigurationManagerInterface $configManager The configuration manager.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationManagerInterface $configManager
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->configManager = $configManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('add:project')
            ->setDescription('Adds a new project.')
            ->addArgument(
                'project_name',
                InputArgument::REQUIRED,
                'The unique name of the project.'
            )
            ->addArgument(
                'project_path',
                InputArgument::REQUIRED,
                'The absolute path of the project.'
            )
            ->addArgument(
                'group_name',
                InputArgument::OPTIONAL,
                'If this argument is used, the project will be grouped using this unique group name.',
                'default'
            )

            ->addOption(
                'foreground_color',
                'fg',
                InputOption::VALUE_REQUIRED,
                'The foreground color of the text output.'
            )
            ->addOption(
                'background_color',
                'bg',
                InputOption::VALUE_REQUIRED,
                'The background color of the text output.'
            );
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $input->getArgument('project_name');
        $projectPath = $input->getArgument('project_path');
        $groupName = $input->getArgument('group_name');

        $foregroundColor = $input->getOption('foreground_color');
        $backgroundColor = $input->getOption('background_color');

        if (!$this->configManager->addProject($projectName, $projectPath, $groupName, $foregroundColor, $backgroundColor)) {
            $this->logger->error('Failed to add the project.', ['arguments' => $input->getArguments()]);

            $output->writeln('<error>Failed to add the project.</error>');
        } else {
            $output->writeln(
                sprintf(
                    '<info>Successfully added the project "%s" to the group "%s".</info>',
                    $projectName,
                    $groupName
                )
            );
        }
    }
}
