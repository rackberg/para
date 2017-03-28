<?php
/**
 * @file
 * Contains lrackwitz\Para\Command\OpenShellCommand.php.
 */

namespace lrackwitz\Para\Command;

use lrackwitz\Para\Service\ConfigurationManagerInterface;
use lrackwitz\Para\Service\ShellFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OpenShellCommand.
 *
 * @package lrackwitz\Para\Command
 */
class OpenShellCommand extends Command
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The shell factory.
     *
     * @var ShellFactory
     */
    private $shellFactory;

    /**
     * The configuration manager.
     *
     * @var ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * OpenShellCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \lrackwitz\Para\Service\ShellFactory $shellFactory The shell factory.
     * @param \lrackwitz\Para\Service\ConfigurationManagerInterface $configManager The configuration manager.
     */
    public function __construct(
        LoggerInterface $logger,
        ShellFactory $shellFactory,
        ConfigurationManagerInterface $configManager
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->shellFactory = $shellFactory;
        $this->configManager = $configManager;
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

        $shell->run($group, $excludedProjects);

        $output->writeln('<info>Finished para shell.</info>');
    }
}
