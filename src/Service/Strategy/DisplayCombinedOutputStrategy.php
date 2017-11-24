<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\Strategy\DisplayCombinedOutputStrategy.php.
 */

namespace lrackwitz\Para\Service\Strategy;

use lrackwitz\Para\Entity\Project;
use lrackwitz\Para\Event\IncrementalOutputReceivedEvent;
use lrackwitz\Para\Event\PostProcessCreationEvent;
use lrackwitz\Para\Service\Output\BufferedOutputInterface;
use lrackwitz\Para\Service\ProcessFactory;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

/**
 * Class DisplayCombinedOutputStrategy.
 *
 * @package lrackwitz\Para\Service\Strategy
 */
class DisplayCombinedOutputStrategy extends DefaultDisplayStrategy implements AsyncShellCommandExecuteStrategy
{

    /**
     * An array of temporary stored chars.
     *
     * @var string[]
     */
    private $tmpChar = [];

    /**
     * A flag that indicates if text has been written into the output buffer.
     *
     * @var bool
     */
    private $writtenToBuffer = false;

    /**
     * DisplayCombinedOutputStrategy constructor.
     *
     * @param \lrackwitz\Para\Service\ProcessFactory $processFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(
        ProcessFactory $processFactory,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($processFactory, $dispatcher);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $cmd, array $projects, BufferedOutputInterface $output)
    {
        // For each project start an asynchronous process.
        /** @var \lrackwitz\Para\Entity\Project $project */
        foreach ($projects as $project) {
            $process = $this->createProcess($cmd, $project->getPath());

            // Dispatch an event to be able to easily configure a process when needed.
            $event = new PostProcessCreationEvent($process, $project);
            $this->dispatcher->dispatch(
                PostProcessCreationEvent::NAME,
                $event
            );

            $this->addProcessInfo($process, $project);
        }

        $this->handleProcesses($output);
    }

    /**
     * Adds the process and project info.
     *
     * @param \Symfony\Component\Process\Process $process
     * @param \lrackwitz\Para\Entity\Project $project
     */
    private function addProcessInfo(Process $process, Project $project)
    {
        $this->processes[$project->getName()] = [
            'process' => $process,
            'project' => $project,
        ];
    }

    protected function handleProcesses(BufferedOutputInterface $output)
    {
        do {
            foreach ($this->processes as $projectName => $processInfo) {
                $this->handleProcessOutput(
                    $processInfo,
                    $output,
                    // Called when the process terminated.
                    function () use ($projectName) {
                        // Remove the process from the list of running processes.
                        unset($this->processes[$projectName]);
                    }
                );
            }

            // Show the whole output.
            $output->flush();
        } while (!empty($this->processes));
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
     * @param string $incrementalOutput
     * @param Project $project
     */
    private function workaroundForSingleChar(
        string &$incrementalOutput,
        Project $project
    ) {
        // Check if there is a temporarily stored char.
        if (!empty($this->tmpChar[$project->getName()]) && !empty($incrementalOutput)) {
            // Add the char directly at the beginning of the current
            // incremental output value.
            $incrementalOutput = $this->tmpChar[$project->getName()] . $incrementalOutput;
            // Clear the temporarily stored char.
            $this->tmpChar[$project->getName()] = '';
        }
        // If there is a single char store it temporarily.
        if (strlen($incrementalOutput) == 1) {
            $this->tmpChar[$project->getName()] = $incrementalOutput;
            // Clear the current incremental output value so that nothing will be written to the console.
            $incrementalOutput = '';
        }
    }

    /**
     * Handles the output for a process.
     *
     * @param array $processInfo The process information.
     * @param \lrackwitz\Para\Service\Output\BufferedOutputInterface $output
     * @param callable $processTerminatedCallback
     */
    protected function handleProcessOutput(
        array $processInfo,
        BufferedOutputInterface $output,
        callable $processTerminatedCallback
    ) {
        // Get the process.
        /** @var Process $process */
        $process = $processInfo['process'];

        // Get the project.
        /** @var Project $project */
        $project = $processInfo['project'];

        // Start the process if not already started.
        if (!$process->isStarted()) {
            $process->start();
        }

        // Get the last output from the process.
        $incrementalOutput = $this->getIncrementalProcessOutput($process);

        $this->workaroundForSingleChar(
            $incrementalOutput,
            $project
        );

        // Show the output.
        if ($incrementalOutput != '') {
            // Dispatch an event.
            $this->dispatcher->dispatch(
                IncrementalOutputReceivedEvent::NAME,
                new IncrementalOutputReceivedEvent(
                    $incrementalOutput,
                    $project,
                    $process
                )
            );

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                if ($project->getColorCode() <= 0) {
                    $output->write(
                        sprintf(
                            '%s:' . "\t" . '%s',
                            $project->getName(),
                            $incrementalOutput
                        )
                    );
                } else {
                    if ($this->writtenToBuffer) {
                        $output->write("\n");
                    }
                    $output->write(
                        sprintf(
                            "\033[38;5;%dm".'%s:'."\033[0m\t".'%s',
                            $project->getColorCode(),
                            $project->getName(),
                            $incrementalOutput
                        )
                    );
                    $this->writtenToBuffer = true;
                }
            }
        }


        if ($process->isTerminated()) {
            $processTerminatedCallback();
        }
    }
}
