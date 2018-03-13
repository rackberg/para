<?php

namespace Para\Command;

use Para\Configuration\GroupConfigurationInterface;
use Para\Exception\GroupNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteGroupCommand.
 *
 * @package Para\Command
 */
class DeleteGroupCommand extends Command
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The group configuration.
     *
     * @var GroupConfigurationInterface
     */
    private $groupConfiguration;

    /**
     * The full path to the config file.
     *
     * @var string
     */
    private $configFile;

    /**
     * EditGroupCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param GroupConfigurationInterface $groupConfiguration The group configuration.
     * @param string $configFile The full path to the config file.
     */
    public function __construct(
        LoggerInterface $logger,
        GroupConfigurationInterface $groupConfiguration,
        string $configFile
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->groupConfiguration = $groupConfiguration;
        $this->configFile = $configFile;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('delete:group')
            ->setDescription('Deletes an existing group and all of it\'s projects in the configuration.')
            ->addArgument(
                'group_name',
                InputArgument::REQUIRED,
                'The name of the group to delete.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $groupName = $input->getArgument('group_name');

        try {
            $this->groupConfiguration->load($this->configFile);
            $this->groupConfiguration->deleteGroup($groupName);
            $this->groupConfiguration->save($this->configFile);
        } catch (GroupNotFoundException $e) {
            $output->writeln('<error>The group you are trying to delete is ' .
                'not stored in the configuration.</error>', 1);

            $this->logger->error('Failed to delete the group.', [
                'groupName' => $groupName,
            ]);
            $output->writeln('<error>Failed to delete the group "' . $groupName . '".', 1);

            return;
        }

        $output->writeln('<info>Successfully deleted the group from the configuration.</info>');
    }
}
