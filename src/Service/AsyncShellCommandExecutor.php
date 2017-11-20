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

        // Determine the current system environment variables.
        $env = $this->discoverSystemEnvironment();

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

            // Add the para_project environment variable.
            $env['para_project'] = $project;

            $process = $this->processFactory->create($cmd, $project_path, $env);
            $process->setTimeout(null);
            $process->start();

            $processes[$process->getPid()] = [
                'process' => $process,
                'project' => $project,
                'color' => $color,
                'log' => $logFile,
            ];
        }

        $tmpChar = [];

        // Loop through the registered processes until every process has terminated.
        do {
            $incrementalOutput = [];

            foreach ($processes as $processId => $processData) {
                // Get the process.
                /** @var Process $process */
                $process = $processData['process'];

                // Get the last output from the process.
                $incrementalOutput[$processData['project']] = $process->getIncrementalOutput();

                // Get the last error output from the process.
                $incrementalErrorOutput = $process->getIncrementalErrorOutput();

                if ($incrementalErrorOutput != '') {
                    $incrementalOutput[$processData['project']] = $incrementalErrorOutput;
                }

                $this->workaroundForSingleChar(
                    $incrementalOutput,
                    $tmpChar,
                    $processData['project']
                );

                // Make sure the output does not contain unwanted ansi escape sequences.
                $incrementalOutput[$processData['project']] =
                    $this->sanitizeOutput($incrementalOutput[$processData['project']]);

                // Only if the output contains data we want to show it.
                if ($incrementalOutput[$processData['project']] != '') {
                    // Add the log output to the log file.
                    if (!file_put_contents(
                        $processData['log'],
                        $incrementalOutput[$processData['project']],
                        FILE_APPEND
                    )) {
                        $this->logger->error('Failed to write log file for project.', ['processData' => $processData]);
                    }

                    if ($output->isDebug()) {
                        $output->write(
                            sprintf(
                                'PID: %s  ->  %s',
                                $processId,
                                $incrementalOutput[$processData['project']]
                            )
                        );
                    } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                        if (!$processData['color']) {
                            $output->write(
                                sprintf(
                                    '%s:' . "\t" . '%s',
                                    $processData['project'],
                                    $incrementalOutput[$processData['project']]
                                )
                            );
                        } else {
                            $output->write(
                                sprintf(
                                    '%s<fg=%s>%s:</>'."\t".'%s',
                                    $this->getNewLineIfRequired($processData['project']) ?: '',
                                    $processData['color'],
                                    $processData['project'],
                                    $incrementalOutput[$processData['project']]
                                )
                            );
                        }
                    }

                    // Save the last output string.
                    $this->lastOutput[$processData['project']] = $incrementalOutput[$processData['project']];
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
        $stringTrimmed = trim($string, "\x08 ");
        if (strlen($stringTrimmed) <= 2 && $stringTrimmed != '') {
            $stringTrimmed = "\033[31m" . $stringTrimmed . "\033[0m";
        }
        return $stringTrimmed;
    }

    /**
     * Returns "\n" if the last output string not ended with "\n".
     *
     * @return string|null The string "\n" or null.
     */
    private function getNewLineIfRequired($project)
    {
        // Check if the last output string ends with new line.
        if (!empty($this->lastOutput[$project])
            && $this->lastOutput[$project][strlen($this->lastOutput[$project]) - 1] != "\n") {
            return "\n";
        }
    }

    /**
     * Discovers the current system environment and returns it.
     *
     * @return string[] The environment variables.
     */
    private function discoverSystemEnvironment()
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

    /**
     * Workaround for single char.
     *
     * Sometimes it happens that only one char has been returned by $process->getIncrementalOutput()
     * even if there are more chars that could also be returned.
     * In this case we need to create a workaround, that temporarily stores
     * the single char returned to output it in front of the next
     * incremental output value.
     *
     * @param array $incrementalOutput
     * @param array $tmpChar
     * @param string $project
     */
    private function workaroundForSingleChar(
        &$incrementalOutput,
        array &$tmpChar,
        $project
    ) {
        // Check if there is a temporarily stored char.
        if (!empty($tmpChar[$project]) && !empty($incrementalOutput[$project])) {
            // Add the char directly at the beginning of the current
            // incremental output value.
            $incrementalOutput[$project] = $tmpChar[$project] . $incrementalOutput[$project];

            // Clear the temporarily stored char.
            $tmpChar[$project] = '';
        }

        // If there is a single char store it temporarily.
        if (strlen($incrementalOutput[$project]) == 1) {
            $tmpChar[$project] = $incrementalOutput[$project];

            // Clear the current incremental output value so that nothing will be written to the console.
            $incrementalOutput[$project] = '';
        }
    }
}
