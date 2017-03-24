<?php
/**
 * @file
 * Contains lrackwitz\Para\Command\EditGroupCommand.php.
 */

namespace lrackwitz\Para\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Class EditGroupCommand.
 *
 * @package lrackwitz\Para\Command
 */
class EditGroupCommand extends Command
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EditGroupCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('edit:group')
            ->setDescription('Edits an existing group in the configuration.')
            ->addArgument(
                'group_name',
                InputArgument::REQUIRED,
                'The name of the group to edit group name.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('Executing command add:group', [ 'input' => $input, 'output' => $output]);
    }
}
