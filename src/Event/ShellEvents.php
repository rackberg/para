<?php
/**
 * @file
 * Contains Para\Event\ShellEvents.php.
 */

namespace Para\Event;

/**
 * Class ShellEvents.
 *
 * @package Para\Event
 */
class ShellEvents
{
    /**
     * This event should be triggered before the shell executes a command.
     *
     * @Event("Para\Event\BeforeShellCommandExecutionEvent")
     */
    const BEFORE_SHELL_COMMAND_EXECUTION_EVENT = 'para.event.before.shell_command_execute';

    /**
     * This event will be triggered when the user presses a key in the shell.
     *
     * @Event("Para\Event\ShellKeyPressEvent")
     */
    const SHELL_KEY_PRESS_EVENT = 'para.event.shell.key_press_event';
}
