<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\ShellHistory.php.
 */

namespace lrackwitz\Para\Service;

/**
 * Class ShellHistory.
 *
 * @package lrackwitz\Para\Service
 */
class ShellHistory implements ShellHistoryInterface
{
    /**
     * The array containing the commands.
     *
     * @var array
     */
    private $commands = [];

    /**
     * Adds a command to the end of the history.
     *
     * @param string $command The command
     */
    public function addCommand($command)
    {
        $this->commands[] = $command;
    }

    /**
     * Clears the history.
     */
    public function clear()
    {
        $this->commands = [];
    }

    /**
     * Returns the last command.
     *
     * @return string The command.
     */
    public function getLastCommand()
    {
        $commands = array_values(array_slice($this->commands, -1));
        if ($commands) {
            return $commands[0];
        }
        return '';
    }

    /**
     * Returns the previous command.
     *
     * @return string The command.
     */
    public function getPreviousCommand()
    {
        if (prev($this->commands)) {
            return $this->getCurrentCommand();
        }
        return '';
    }

    /**
     * Returns the next command.
     *
     * @return string The command.
     */
    public function getNextCommand()
    {
        if (next($this->commands)) {
            return $this->getCurrentCommand();
        }
        return '';
    }

    /**
     * Returns the current command.
     *
     * @return string The command.
     */
    public function getCurrentCommand()
    {
        return current($this->commands) ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommands(array $commands)
    {
        $this->commands = $commands;
    }
}
