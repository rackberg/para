<?php
/**
 * @file
 * Contains lrackwitz\Para\Event\BeforeShellCommandExecutionEvent.php.
 */

namespace lrackwitz\Para\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class BeforeCommandExecutionEvent.
 *
 * @package lrackwitz\Para\Event
 */
class BeforeShellCommandExecutionEvent extends Event
{
    /**
     * The shell command.
     *
     * @var string
     */
    private $command;

    /**
     * BeforeShellCommandExecutionEvent constructor.
     *
     * @param string $command The shell command.
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * Returns the command.
     *
     * @return string The command.
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Sets the command.
     *
     * @param string $command The command.
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }
}
