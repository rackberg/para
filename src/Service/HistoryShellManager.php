<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\HistoryShellManager.php.
 */

namespace lrackwitz\Para\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class HistoryShellManager.
 *
 * @package lrackwitz\Para\Service
 */
class HistoryShellManager implements HistoryShellManagerInterface
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The shell history.
     *
     * @var ShellHistoryInterface
     */
    private $history;

    /**
     * The prompt.
     *
     * @var string
     */
    private $prompt;

    /**
     * The current user input.
     *
     * @var string
     */
    private $userInput;

    /**
     * The current cursor position.
     *
     * @var int
     */
    private $cursorPosition = 0;

    /**
     * The number of times the up arrow has been pressed.
     *
     * @var int
     */
    private $countUpPressed = 0;

    /**
     * Flag to indicate if the history end is reached.
     *
     * @var bool
     */
    private $historyEndReached = false;

    public function __construct(
        LoggerInterface $logger,
        ShellHistoryInterface $history
    ) {
        $this->logger = $logger;
        $this->history = $history;
    }

    /**
     * Reads the input from the input stream.
     *
     * @param resource
     *
     * @return string The input line.
     */
    public function readInput($inputStream = STDIN)
    {
        $output = new ConsoleOutput();

        $sttyMode = shell_exec('stty -g');

        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');

        $this->userInput = '';
        $this->cursorPosition = 0;

        // Make sure the array cursor of the history is at the last element.
        if (($commands = $this->history->getCommands()) != []) {
            end($commands);
            $this->history->setCommands($commands);
        }

        // Reset the counter for the up arrow.
        $this->countUpPressed = 0;

        // Read a keypress
        while (!feof($inputStream)) {
            $c = fread($inputStream, 1);

            // Backspace Character
            if ("\177" === $c) {
                $this->onBackspacePressed();

            } elseif ("\033" === $c) {
                // Did we read an escape sequence?
                $c .= fread($inputStream, 2);

                // A = Up Arrow. B = Down Arrow
                if (isset($c[2]) && ('A' === $c[2] || 'B' === $c[2])) {
                    // Clear the current line.
                    $this->clearLine();

                    if ('A' === $c[2]) {
                        $this->onUpArrowPressed();
                    } elseif ('B' === $c[2]) {
                        $this->onDownArrowPressed();
                    }

                } elseif (isset($c[2]) && ('D' === $c[2])) {
                    $this->onLeftArrowPressed();
                } elseif (isset($c[2]) && ('C' === $c[2])) {
                    $this->onRightArrowPressed();
                }
                // Enter key pressed.
            } elseif (ord($c) < 32) {
                if ("\t" === $c || "\n" === $c) {
                    if ("\n" === $c) {
                        $this->onEnterPressed();
                        break;
                    }
                }

                continue;
            } else {
                $this->userInput =
                    substr($this->userInput, 0, $this->cursorPosition) . $c .
                    substr($this->userInput, $this->cursorPosition);

                $this->cursorPosition++;
            }

            $this->printCurrentUserInput();
        }

        // Reset stty so it behaves normally again
        shell_exec(sprintf('stty %s', $sttyMode));

        $userInput = trim($this->userInput);

        return $userInput;
    }

    public function onBackspacePressed()
    {
        if ($this->cursorPosition > 0) {
            $this->userInput =
                substr($this->userInput, 0, $this->cursorPosition - 1) .
                substr($this->userInput, $this->cursorPosition);

            $this->cursorPosition--;
        }
    }

    public function clearLine()
    {
        $output = new ConsoleOutput();

        if ($this->cursorPosition > strlen($this->prompt)) {
            for ($i = strlen(
                    $this->prompt
                ) - $this->cursorPosition; $this->cursorPosition > 0; $i--) {
                $output->write("\033[1D");
                $this->cursorPosition--;
            }
            $output->write($this->prompt);
            $this->cursorPosition = strlen($this->prompt);
        }
    }

    public function onUpArrowPressed()
    {
        if ($this->countUpPressed == 0) {
            $command = $this->history->getLastCommand();
            $this->countUpPressed++;
        } elseif ($this->historyEndReached) {
            $command = $this->history->getCurrentCommand();
            $this->historyEndReached = false;
        } else {
            $command = $this->history->getPreviousCommand();
        }

        if ($command) {
            $this->userInput = $command;
            $this->cursorPosition = strlen($command);
        } else {
            // Set the array cursor to the first element.
            $commands = $this->getHistory()->getCommands();
            reset($commands);
            $this->getHistory()->setCommands($commands);

            $command = $this->getHistory()->getCurrentCommand();
            $this->userInput = $command;
            $this->cursorPosition = strlen($command);
        }
    }

    public function onDownArrowPressed()
    {
        if ($command = $this->history->getNextCommand()) {
            $this->userInput = $command;
            $this->cursorPosition = strlen($command);

            // Set flag that the history end has not reached yet.
            $this->historyEndReached = false;
        } else {
            $this->userInput = '';
            $this->cursorPosition = 0;

            // Set the array cursor to the last element.
            $commands = $this->getHistory()->getCommands();
            end($commands);
            $this->getHistory()->setCommands($commands);

            // Set flag that the history end is reached.
            $this->historyEndReached = true;
        }
    }

    public function onEnterPressed()
    {
        $output = new ConsoleOutput();
        $output->write("\n");
    }

    public function onLeftArrowPressed()
    {
        if ($this->cursorPosition > 0) {
            $this->cursorPosition = max(0, $this->cursorPosition - 1);
        }
    }

    public function onRightArrowPressed()
    {
        $this->cursorPosition = min(strlen($this->userInput), $this->cursorPosition + 1);
    }

    private function printCurrentUserInput()
    {
        $output = new ConsoleOutput();

        // Move all the way left.
        $output->write("\033[1000D");

        // Clear the line.
        $output->write("\033[0K");

        // Write the prompt with user input.
        $output->write($this->prompt . $this->userInput);

        // Move all the way left again.
        $output->write("\033[1000D");

        if ($this->cursorPosition >= 0) {
            // Move cursor to index.
            $output->write("\033[" . (strlen($this->stripUnicodeChars($this->prompt)) + $this->cursorPosition) . "C");
        }
    }

    /**
     * Strips all unicode characters from a string.
     *
     * @param string $string The string to strip unicode characters from.
     *
     * @return string The stripped string or the original string.
     */
    private function stripUnicodeChars($string)
    {
        $new_string = preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $string);
        if ($new_string) {
            return $new_string;
        }
        return $string;
    }

    /**
     * The prompt.
     *
     * @param string $prompt The prompt.
     */
    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;
    }

    /**
     * Returns the shell history.
     *
     * @return ShellHistoryInterface
     */
    public function getHistory()
    {
        return $this->history;
    }
}
