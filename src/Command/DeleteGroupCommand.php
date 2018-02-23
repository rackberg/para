<?php
/**
 * @file
 * Contains Para\Command\DeleteGroupCommand.php.
 */

namespace Para\Command;

use Para\Exception\GroupNotFoundException;
use Para\Service\ConfigurationManagerInterface;
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
     * The configuration manager.
     *
     * @var ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * EditGroupCommand constructor.
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
        $group = $input->getArgument('group_name');

        $ret = false;
        try {
            $ret = $this->configManager->deleteGroup($group);
        } catch (GroupNotFoundException $e) {
            $output->writeln('<error>The group you are trying to delete is ' .
                'not stored in the configuration.</error>', 1);
        }

        if (!$ret) {
            $this->logger->error('Failed to delete the group.', [
                'groupName' => $group,
            ]);
            $output->writeln('<error>Failed to delete the group "' . $group . '".', 1);
        } else {
            $output->writeln('<info>Successfully deleted the group from the configuration.</info>');
        }
    }
}
