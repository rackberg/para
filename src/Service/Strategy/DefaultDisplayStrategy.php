<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\Strategy\DefaultDisplayStrategy.php.
 */

namespace lrackwitz\Para\Service\Strategy;

use lrackwitz\Para\Event\PostProcessCreationEvent;
use lrackwitz\Para\Service\Output\BufferedOutputInterface;
use lrackwitz\Para\Service\ProcessFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

/**
 * Class DefaultDisplayStrategy.
 *
 * @package lrackwitz\Para\Service\Strategy
 */
abstract class DefaultDisplayStrategy
{
    /**
     * The process factory.
     *
     * @var \lrackwitz\Para\Service\ProcessFactory
     */
    protected $processFactory;

    /**
     * The event dispatcher.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Holds the running processes and additional information.
     *
     * @var string[]
     */
    protected $processes;

    /**
     * DefaultDisplayStrategy constructor.
     *
     * @param \lrackwitz\Para\Service\ProcessFactory $processFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(
        ProcessFactory $processFactory,
        EventDispatcherInterface $dispatcher
    ) {
        $this->processFactory = $processFactory;
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * Sanitizes the output string.
     *
     * @param string $string The output to sanitize.
     *
     * @return string The sanitized output string.
     */
    protected function sanitizeOutput($string)
    {
        return trim($string, " \t\n\r\0\x0B\x08");
    }

    /**
     * Creates a new process for the execution of a command.
     *
     * @param string $cmd The command to execute.
     * @param string $cwd The current working directory.
     *
     * @return \Symfony\Component\Process\Process The created process.
     */
    protected function createProcess(string $cmd, string $cwd): Process
    {
        $process = $this->processFactory->create(
            $cmd,
            $cwd,
            $this->discoverSystemEnvironment()
        );
        $process->setTimeout(null);

        return $process;
    }

    /**
     * Moves the cursor up the given number of steps.
     *
     * @param int $steps The number of steps to move the cursor up.
     * @param \lrackwitz\Para\Service\Output\BufferedOutputInterface $output The output
     */
    protected function moveCursorUp(int $steps, BufferedOutputInterface $output)
    {
        $output->write(sprintf("\033[%dA", $steps));
    }

    /**
     * Moves the cursor down the given number of steps.
     *
     * @param int $steps The number of steps to move the cursor down.
     * @param \lrackwitz\Para\Service\Output\BufferedOutputInterface $output The output.
     */
    protected function moveCursorDown(int $steps, BufferedOutputInterface $output)
    {
        $output->write(sprintf("\033[%dB", $steps));
    }

    /**
     * Returns the last incremental output (stdout, stderr) of a process.
     *
     * @param \Symfony\Component\Process\Process $process The process.
     *
     * @return string The output of the process.
     */
    protected function getIncrementalProcessOutput(Process $process): string
    {
        // Get the last stdout output from the process.
        $incrementalOutput = $process->getIncrementalOutput();

        // Get the last error output from the process.
        $incrementalErrorOutput = $process->getIncrementalErrorOutput();

        if ($incrementalErrorOutput != '') {
            $incrementalOutput = $incrementalErrorOutput;
        }

        // Make sure the output does not contain unwanted ansi escape sequences.
        return $this->sanitizeOutput($incrementalOutput);
    }

    /**
     * Discovers the current system environment and returns it.
     *
     * @return string[] The environment variables.
     */
    protected function discoverSystemEnvironment()
    {
        $env = [];
        $output = shell_exec('env');
        if ($output) {
            $lines = explode("\n", $output);
            foreach ($lines as $line) {
                $values = explode('=', $line);
                if (!empty($values[0]) && !empty($values[1])) {
                    $env[$values[0]] = $values[1];
                }
            }
        }
        return $env;
    }


    abstract protected function handleProcesses(BufferedOutputInterface $output);
}
