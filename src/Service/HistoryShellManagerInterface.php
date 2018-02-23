<?php
/**
 * @file
 * Contains Para\Service\HistoryShellManagerInterface.php.
 */

namespace Para\Service;

/**
 * Interface HistoryShellManagerInterface.
 *
 * @package Para\Service
 */
interface HistoryShellManagerInterface extends ShellManagerInterface
{
    /**
     * Returns the shell history.
     *
     * @return ShellHistoryInterface
     */
    public function getHistory();

    /**
     * Sets the current user input.
     *
     * @param string $userInput
     */
    public function setUserInput(string $userInput);

    /**
     * Returns the current user input.
     *
     * @return string
     */
    public function getUserInput();

    /**
     * Called when the up arrow has been pressed.
     */
    public function onUpArrowPressed();

    /**
     * Called when the down arrow has been pressed.
     */
    public function onDownArrowPressed();
}
