<?php
/**
 * @file
 * Contains Para\Service\Strategy\DisplayProgressbarStrategy.php.
 */

namespace Para\Service\Strategy;

use Para\Entity\Project;
use Para\Service\Output\BufferedOutputInterface;
use Para\Factory\ProcessFactoryInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

/**
 * Class DisplayProgressbarStrategy.
 *
 * @package Para\Service\Strategy
 */
class DisplayProgressbarStrategy extends DefaultDisplayStrategy implements AsyncShellCommandExecuteStrategy
{
    /**
     * The current index.
     *
     * @var int
     */
    private $currentIndex = 0;

    /**
     * DisplayProgressbarStrategy constructor.
     *
     * @param \Para\Factory\ProcessFactoryInterface $processFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(
        ProcessFactoryInterface $processFactory,
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
        $i = 0;
        /** @var \Para\Entity\Project $project */
        foreach ($projects as $project) {
            $process = $this->createProcess($cmd, $project->getPath());
            $progressBar = $this->createProgressBar($output, $project);

            $this->addProcessInfo($process, $project, $progressBar, $i++);
        }

        $this->startProgressBars($output);

        // Loop through the registered processes until every process has terminated.
        $this->handleProcesses($output);
    }

    /**
     * Adds the process to the list of processes.
     *
     * @param \Symfony\Component\Process\Process $process The process to add.
     * @param \Para\Entity\Project $project The project.
     * @param \Symfony\Component\Console\Helper\ProgressBar $progressBar The progress bar.
     * @param int $index The current index.
     */
    private function addProcessInfo(
        Process $process,
        Project $project,
        ProgressBar $progressBar,
        int $index
    ) {
        $this->processes[$project->getName()] = [
            'process' => $process,
            'project' => $project,
            'progressBar' => $progressBar,
            'index' => $index,
        ];
    }

    /**
     * Creates a new progress bar for a project.
     *
     * @param \Para\Service\Output\BufferedOutputInterface $output Used to write the progress bar to the terminal.
     * @param Project $project The project.

     * @return \Symfony\Component\Console\Helper\ProgressBar The created progressbar.
     */
    private function createProgressBar(
        BufferedOutputInterface $output,
        Project $project
    ): ProgressBar {
        $progressBar = new ProgressBar($output, 1);
        $progressBar->setFormatDefinition('custom', '%project%: [%bar%] %message%');
        $progressBar->setFormat('custom');
        $progressBar->setMessage(
            sprintf(
                "\033[38;5;%sm%s\033[0m",
                $project->getColorCode(),
                $project->getName()
            ),
            'project'
        );

        return $progressBar;
    }

    /**
     * Starts and renders the progress bars.
     *
     * @param \Para\Service\Output\BufferedOutputInterface $output
     */
    private function startProgressBars(BufferedOutputInterface $output)
    {
        // Start and display the progress bars.
        foreach ($this->processes as $processInfo) {
            $processInfo['progressBar']->start();
            $output->write("\n");

            // Set the current index. For simplicity we overwrite the value each time.
            $this->currentIndex = $processInfo['index'];
        }
    }

    /**
     * Sets the cursor to the line where the progress bar with given index is displayed.
     *
     * @param int $index The progress bar index.
     * @param \Para\Service\Output\BufferedOutputInterface $output
     */
    private function setCursorToProgressBar(int $index, BufferedOutputInterface $output)
    {
        if ($this->currentIndex > $index) {
            // Move the cursor up.
            $this->moveCursorUp($this->currentIndex - $index, $output);
        } elseif ($this->currentIndex < $index) {
            // Move the cursor down.
            $this->moveCursorDown($index - $this->currentIndex, $output);
        }
    }

    private function handleProcessOutput($processInfo, BufferedOutputInterface $output, callable $processTerminatedCallback)
    {
        // Get the process.
        /** @var Process $process */
        $process = $processInfo['process'];

        // Get the progress bar.
        /** @var ProgressBar $progressBar */
        $progressBar = $processInfo['progressBar'];

        // Start the process now!
        if (!$process->isStarted()) {
            $process->start();
        }

        if ($process->isRunning()) {
            // Get the last output from the process.
            $incrementalOutput = $this->getIncrementalProcessOutput($process);

            // Only if the output contains data we want to show it.
            if ($incrementalOutput != '') {
                // Make sure the cursor is located a the right line.
                $this->setCursorToProgressBar($processInfo['index'], $output);

                // Show the first 50 chars of the message.
                $progressBar->setMessage(substr($incrementalOutput, 0, 50));

                // Advance the progress bar of the current process.
                $progressBar->advance();

                // Move the cursor to the left.
                $output->write("\033[1000D");
            }
        }

        if ($process->isTerminated()) {
            $processTerminatedCallback($process, $progressBar);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function handleProcesses(BufferedOutputInterface $output)
    {
        do {
            foreach ($this->processes as $projectName => $processInfo) {
                $this->handleProcessOutput(
                    $processInfo,
                    $output,
                    // Called when the process terminated.
                    function (Process $process, ProgressBar $progressBar) use ($projectName, $processInfo, $output) {
                        // Make sure the cursor is located a the right line.
                        $this->setCursorToProgressBar($processInfo['index'], $output);

                        // Finish the progress bar for the current process.
                        $progressBar->setMessage('Finished execution');
                        $progressBar->finish();

                        // Move the cursor to the left.
                        $output->write("\033[1000D");

                        // ... remove the process from the array.
                        unset($this->processes[$projectName]);
                    }
                );
            }

            if (empty($this->processes)) {
                $this->moveCursorDown(10000, $output);
                $output->write("\n");
            }

            // Flush the output buffer.
            $output->flush();
        } while (!empty($this->processes));
    }
}
