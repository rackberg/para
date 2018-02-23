<?php
/**
 * @file
 * Contains Para\Service\ShellHistoryInterface.php.
 */

namespace Para\Service;

/**
 * Interface ShellHistoryInterface.
 *
 * @package Para\Service
 */
interface ShellHistoryInterface
{
    /**
     * Adds a command to the end of the history.
     *
     * @param string $command The command
     */
    public function addCommand($command);

    /**
     * Clears the history.
     */
    public function clear();

    /**
     * Returns the last command.
     *
     * @return string The command.
     */
    public function getLastCommand();

    /**
     * Returns the previous command.
     *
     * @return string The command.
     */
    public function getPreviousCommand();

    /**
     * Returns the next command.
     *
     * @return string The command.
     */
    public function getNextCommand();

    /**
     * Returns the current command.
     *
     * @return string The command.
     */
    public function getCurrentCommand();

    /**
     * Returns the commands.
     *
     * @return array The array containing the commands.
     */
    public function getCommands();

    /**
     * Sets the commands.
     *
     * @param array $commands An array containing commands.
     */
    public function setCommands(array $commands);

    /**
     * Loads the shell command history stored in a file.
     *
     * @param string $file The full path to the file.
     */
    public function loadHistory($file);

    /**
     * Saves the shell command history in a file.
     *
     * @param string $file The full path to the file.
     */
    public function saveHistory($file);
}
