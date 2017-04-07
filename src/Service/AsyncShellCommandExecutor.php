<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\AsyncShellCommandExecutor.php.
 */

namespace lrackwitz\Para\Service;

use lrackwitz\Para\Service\Output\BufferedOutputInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class AsyncShellCommandExecutor.
 *
 * @package lrackwitz\Para\Service
 */
class AsyncShellCommandExecutor
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The process factory.
     *
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * The path to save log files.
     *
     * @var string
     */
    private $logPath;

    /**
     * The colors available.
     *
     * @var array
     */
    private $availableColors = ['red', 'green', 'yellow', 'blue', 'magenta', 'cyan'];

    /**
     * The last output string.
     *
     * @var string
     */
    private $lastOutput;

    /**
     * AsyncShellCommandExecutor constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \lrackwitz\Para\Service\ProcessFactory $processFactory The process factory.
     * @param string $logPath The path to save log files.
     */
    public function __construct(
        LoggerInterface $logger,
        ProcessFactory $processFactory,
        $logPath
    ) {
        $this->logger = $logger;
        $this->processFactory = $processFactory;
        $this->logPath = $logPath;
    }

    /**
     * Executes a shell command asynchronously in all registered projects.
     *
     * The output of every project will be shown as soon as possible.
     * For every project a log file will be created.
     *
     * @param string $cmd The shell command.
     * @param array $projects The array of projects.
     * @param \lrackwitz\Para\Service\Output\BufferedOutputInterface $output The output buffer.
     */
    public function execute($cmd, array $projects, BufferedOutputInterface $output)
    {
        // Get a copy of all available colors.
        $colors = $this->availableColors;

        // This variable keeps track of our running processes.
        /** @var Process[] $processes */
        $processes = [];

        // For each project start an asynchronous process.
        foreach ($projects as $project => $project_path) {
            // Get a color yet not used.
            $color = $this->getUnusedColor($colors);

            // Define the log file.
            $logFile = $this->logPath . strtolower($project) . '.log';
            // Make sure no log file exists for this project.
            if (file_exists($logFile)) {
                unlink($logFile);
            }

            $process = $this->processFactory->create($cmd, $project_path);
            $process->setTimeout(null);
            $process->start();

            $processes[$process->getPid()] = [
                'process' => $process,
                'project' => $project,
                'color' => $color,
                'log' => $logFile,
            ];
        }

        // Loop through the registered processes until every process has terminated.
        do {
            foreach ($processes as $processId => $processData) {
                // Get the process.
                /** @var Process $process */
                $process = $processData['process'];

                // Get the last output from the process.
                $incrementalOutput = $process->getIncrementalOutput();

                // Get the last error output from the process.
                $incrementalErrorOutput = $process->getIncrementalErrorOutput();

                if ($incrementalErrorOutput != '') {
                    $incrementalOutput = $incrementalErrorOutput;
                }

                // Make sure the output does not contain unwanted ansi escape sequences.
                $incrementalOutput = $this->sanitizeOutput($incrementalOutput);

                // Only if the output contains data we want to show it.
                if ($incrementalOutput != '') {
                    // Add the log output to the log file.
                    if (!file_put_contents($processData['log'], $incrementalOutput, FILE_APPEND)) {
                        $this->logger->error('Failed to write log file for project.', ['processData' => $processData]);
                    }

                    if ($output->isDebug()) {
                        $output->write(
                            sprintf(
                                'PID: %s  ->  %s',
                                $processId,
                                $incrementalOutput
                            )
                        );
                    } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                        if (!$processData['color']) {
                            $output->write(
                                sprintf(
                                    '%s:' . "\t" . '%s',
                                    $processData['project'],
                                    $incrementalOutput
                                )
                            );
                        } else {
                            $output->write(
                                sprintf(
                                    '%s<fg=%s>%s:</>'."\t".'%s',
                                    $this->getNewLineIfRequired() ?: '',
                                    $processData['color'],
                                    $processData['project'],
                                    $incrementalOutput
                                )
                            );
                        }
                    }

                    // Save the last output string.
                    $this->lastOutput = $incrementalOutput;
                }

                // When a process terminated...
                if ($process->isTerminated()) {
                    if ($output->isDebug()) {
                        $output->writeln(
                            sprintf(
                                'PID: %s  ->  %s',
                                $processId,
                                'Finished process.'
                            )
                        );
                    }

                    // ... remove the process from the array.
                    unset($processes[$processId]);
                }
            }

            // Flush the output buffer.
            $output->flush();

        } while (!empty($processes));
    }

    /**
     * Returns a yet unused color.
     *
     * @param array $colors The array of colors available.
     *
     * @return string The choosen color.
     */
    private function getUnusedColor(array &$colors)
    {
        // Get the color key value.
        $colorKey = rand(0, count($colors) - 1);

        // Get the color.
        $color = $colors[$colorKey];

        // Remove the color from the colors array.
        unset($colors[$colorKey]);

        // Re-index the colors array.
        $colors = array_values($colors);

        return $color;
    }

    /**
     * Sanitizes the output string.
     *
     * @param string $string The output to sanitize.
     *
     * @return string The sanitized output string.
     */
    private function sanitizeOutput($string)
    {
        return trim($string, "\x08 ");
    }

    /**
     * Returns "\n" if the last output string not ended with "\n".
     *
     * @return string|null The string "\n" or null.
     */
    private function getNewLineIfRequired()
    {
        // Check if the last output string ends with new line.
        if ($this->lastOutput != '' && $this->lastOutput[strlen($this->lastOutput) - 1] != "\n") {
            return "\n";
        }
    }
}
