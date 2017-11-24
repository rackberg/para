<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\Strategy\AsyncShellCommandExecuteStrategy.php.
 */

namespace lrackwitz\Para\Service\Strategy;

use lrackwitz\Para\Service\Output\BufferedOutputInterface;

/**
 * Interface AsyncShellCommandExecuteStrategy.
 *
 * @package lrackwitz\Para\Service\Strategy
 */
interface AsyncShellCommandExecuteStrategy
{
    /**
     * Handles the asynchronous execution of the command in the shell.
     *
     * @param string $cmd The command to execute.
     * @param \lrackwitz\Para\Entity\Project[] $projects The projects for which the command should be executed.
     * @param \lrackwitz\Para\Service\Output\BufferedOutputInterface $output
     */
    public function execute(string $cmd, array $projects, BufferedOutputInterface $output);
}
