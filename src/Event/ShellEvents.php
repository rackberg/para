<?php
/**
 * @file
 * Contains lrackwitz\Para\Event\ShellEvents.php.
 */

namespace lrackwitz\Para\Event;

/**
 * Class ShellEvents.
 *
 * @package lrackwitz\Para\Event
 */
class ShellEvents
{
    /**
     * This event should be triggered before the shell executes a command.
     *
     * @Event("lrackwitz\Para\Event\BeforeShellCommandExecutionEvent")
     */
    const BEFORE_SHELL_COMMAND_EXECUTION_EVENT = 'para.event.before.shell_command_execute';
}