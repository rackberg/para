<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\ShellManagerInterface.php.
 */

namespace lrackwitz\Para\Service;

/**
 * Interface ShellManagerInterface.
 *
 * @package lrackwitz\Para\Service
 */
interface ShellManagerInterface
{
    /**
     * Reads the input from the input stream.
     *
     * @param resource $inputStream The input stream.
     *
     * @return string The input line.
     */
    public function readInput($inputStream = STDIN);

    /**
     * The prompt.
     *
     * @param string $prompt The prompt.
     */
    public function setPrompt($prompt);
}
