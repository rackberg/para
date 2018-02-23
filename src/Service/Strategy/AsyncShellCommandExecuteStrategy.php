<?php
/**
 * @file
 * Contains Para\Service\Strategy\AsyncShellCommandExecuteStrategy.php.
 */

namespace Para\Service\Strategy;

use Para\Service\Output\BufferedOutputInterface;

/**
 * Interface AsyncShellCommandExecuteStrategy.
 *
 * @package Para\Service\Strategy
 */
interface AsyncShellCommandExecuteStrategy
{
    /**
     * Handles the asynchronous execution of the command in the shell.
     *
     * @param string $cmd The command to execute.
     * @param \Para\Entity\Project[] $projects The projects for which the command should be executed.
     * @param \Para\Service\Output\BufferedOutputInterface $output
     */
    public function execute(string $cmd, array $projects, BufferedOutputInterface $output);
}
