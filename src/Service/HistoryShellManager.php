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
        $this->cursorPosition = strlen($this->prompt);

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
                // Normal character.
                $output->write($c);

                // Add the character to the user input variable.
                $this->userInput .= $c;

                // Increment the cursor position.
                $this->cursorPosition++;
            }

            // Erase characters from cursor to end of line
            $output->write("\033[K");
        }

        // Reset stty so it behaves normally again
        shell_exec(sprintf('stty %s', $sttyMode));

        $userInput = trim($this->userInput);

        return $userInput;
    }

    public function onBackspacePressed()
    {
        $output = new ConsoleOutput();

        // Move cursor backwards if it does not erase the prompt.
        if ($this->cursorPosition > strlen($this->prompt)) {
            $output->write("\033[1D");

            // Remove one character from the user input variable.
            if (strlen($this->userInput) > 0) {
                $this->userInput = substr($this->userInput, 0, strlen($this->userInput) - 1);
            }

            $this->cursorPosition--;
        }
    }

    public function clearLine()
    {
        $output = new ConsoleOutput();

        if ($this->cursorPosition > strlen($this->prompt)) {
            for ($i = strlen($this->prompt) - $this->cursorPosition; $this->cursorPosition > 0; $i--) {
                $output->write("\033[1D");
                $this->cursorPosition--;
            }
            $output->write($this->prompt);
            $this->cursorPosition = strlen($this->prompt);
        }
    }

    public function onUpArrowPressed()
    {
        $output = new ConsoleOutput();

        if ($this->countUpPressed == 0) {
            $command = $this->history->getLastCommand();
            $this->countUpPressed++;
        } else {
            $command = $this->history->getPreviousCommand();
        }

        if ($command) {
            $output->write($command);
            $this->cursorPosition += strlen($command);
            $this->userInput = $command;
        } else {
            // Set the array cursor to the first element.
            $commands = $this->getHistory()->getCommands();
            reset($commands);
            $this->getHistory()->setCommands($commands);

            $command = $this->getHistory()->getCurrentCommand();
            $output->write($command);
            $this->cursorPosition += strlen($command);
            $this->userInput = $command;
        }
    }

    public function onDownArrowPressed()
    {
        $output = new ConsoleOutput();

        if ($command = $this->history->getNextCommand()) {
            $output->write($command);
            $this->cursorPosition += strlen($command);
            $this->userInput = $command;
        } else {
            // Set the array cursor back to the last element.
            $commands = $this->getHistory()->getCommands();
            end($commands);
            $this->getHistory()->setCommands($commands);

            $command = $this->getHistory()->getCurrentCommand();
            $output->write($command);
            $this->cursorPosition += strlen($command);
            $this->userInput = $command;
        }
    }

    public function onEnterPressed()
    {
        $output = new ConsoleOutput();
        $output->write("\n");
    }

    public function onLeftArrowPressed()
    {
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
