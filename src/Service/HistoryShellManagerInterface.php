<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\HistoryShellManagerInterface.php.
 */

namespace lrackwitz\Para\Service;

/**
 * Interface HistoryShellManagerInterface.
 *
 * @package lrackwitz\Para\Service
 */
interface HistoryShellManagerInterface extends ShellManagerInterface
{
    /**
     * Returns the shell history.
     *
     * @return ShellHistoryInterface
     */
    public function getHistory();
}
