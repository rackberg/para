<?php
/**
 * @file
 * Contains Para\Event\IncrementalOutputReceivedEvent.php.
 */

namespace Para\Event;

use Para\Entity\Project;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

/**
 * Class IncrementalOutputReceivedEvent.
 *
 * @package Para\Event
 */
class IncrementalOutputReceivedEvent extends Event
{
    const NAME = 'para.event.incremental_output_received';

    /**
     * The incremental output received.
     *
     * @var string
     */
    private $incrementalOutput;

    /**
     * The current project.
     *
     * @var Project
     */
    private $project;

    /**
     * The process that returned the incremental output.
     *
     * @var Process
     */
    private $process;

    /**
     * IncrementalOutputReceivedEvent constructor.
     * @param string $incrementalOutput
     * @param Project $project
     * @param Process $process
     */
    public function __construct(
        $incrementalOutput,
        Project $project,
        Process $process
    ) {
        $this->incrementalOutput = $incrementalOutput;
        $this->project = $project;
        $this->process = $process;
    }

    /**
     * Returns incrementalOutput.
     *
     * @return string
     */
    public function getIncrementalOutput()
    {
        return $this->incrementalOutput;
    }

    /**
     * @param string $incrementalOutput
     */
    public function setIncrementalOutput(string $incrementalOutput)
    {
        $this->incrementalOutput = $incrementalOutput;
    }

    /**
     * Returns project.
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Returns process.
     *
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param Process $process
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;
    }
}
