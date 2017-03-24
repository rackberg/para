<?php
/**
 * @file
 * Contains lrackwitz\Para\Command\DeleteProjectCommand.php.
 */

namespace lrackwitz\Para\Command;

use lrackwitz\Para\Exception\ProjectNotFoundException;
use lrackwitz\Para\Service\ConfigurationManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteProjectCommand.
 *
 * @package lrackwitz\Para\Command
 */
class DeleteProjectCommand extends Command
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
     * DeleteProjectCommand constructor.
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
            ->setName('delete:project')
            ->setDescription('Deletes an existing project from the configuration.')
            ->addArgument(
                'project_name',
                InputArgument::REQUIRED,
                'The name of the project to delete.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project_name');

        $ret = false;
        try {
            $ret = $this->configManager->deleteProject($project);
        } catch (ProjectNotFoundException $e) {
            $output->writeln('<error>The project you are trying to delete is ' .
                'not stored in the configuration.</error>', 1);
        }

        if (!$ret) {
            $this->logger->error('Failed to delete the project.', [
                'projectName' => $project,
            ]);
            $output->writeln('<error>Failed to delete the project "' . $project . '".', 1);
        } else {
            $output->writeln('<info>Successfully deleted the project from the configuration.</info>');
        }
    }


}
