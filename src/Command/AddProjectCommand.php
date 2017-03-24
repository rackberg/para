<?php
/**
 * @file
 * Contains lrackwitz\Para\Command\AddProjectCommand.php.
 */

namespace lrackwitz\Para\Command;

use lrackwitz\Para\Service\ConfigurationManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddProjectCommand.
 *
 * @package lrackwitz\Para\Command
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
     * @param \lrackwitz\Para\Service\ConfigurationManagerInterface $configManager The configuration manager.
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
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project_name');
        $path = $input->getArgument('project_path');
        $group = $input->getArgument('group_name');

        if (!$this->configManager->addProject($project, $path, $group)) {
            $this->logger->error('Failed to add the project.', ['arguments' => $input->getArguments()]);

            $output->writeln('<error>Failed to add the project.</error>');
        } else {
            $output->writeln(
                sprintf(
                    '<info>Successfully added the project "%s" to the group "%s".</info>',
                    $project,
                    $group
                )
            );
        }
    }
}
