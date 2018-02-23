<?php
/**
 * @file
 * Contains Para\Event\PostProcessCreationEvent.php.
 */

namespace Para\Event;

use Para\Entity\Project;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

/**
 * Class PostProcessCreationEvent.
 *
 * @package Para\Event
 */
class PostProcessCreationEvent extends Event
{
    const NAME = 'para.event.post_process_creation';

    /**
     * The created process.
     *
     * @var Process
     */
    private $process;

    /**
     * The current project.
     *
     * @var Project
     */
    private $project;

    /**
     * PostProcessCreationEvent constructor.
     *
     * @param Process $process The created process.
     * @param Project $project The current project.
     */
    public function __construct(Process $process, Project $project)
    {
        $this->process = $process;
        $this->project = $project;
    }

    /**
     * @param Process $process
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;
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
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
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
}
