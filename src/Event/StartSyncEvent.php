<?php
/**
 * @file
 * Contains Para\Event\StartSyncEvent.php.
 */

namespace Para\Event;

use Para\Entity\ProjectInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class StartSyncEvent.
 *
 * @package Para\Event
 */
class StartSyncEvent extends Event
{
    const NAME = 'start.sync';

    /**
     * The source file.
     *
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    private $sourceFile;

    /**
     * The target file.
     *
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    private $targetFile;

    /**
     * The source project.
     *
     * @var ProjectInterface
     */
    private $sourceProject;

    /**
     * The target project.
     *
     * @var ProjectInterface
     */
    private $targetProject;

    /**
     * StartSyncEvent constructor.
     * @param \Symfony\Component\HttpFoundation\File\File $sourceFile
     *   The source file.
     * @param \Symfony\Component\HttpFoundation\File\File $targetFile
     *   The target file.
     */
    public function __construct(File $sourceFile, File $targetFile)
    {
        $this->sourceFile = $sourceFile;
        $this->targetFile = $targetFile;
    }

    /**
     * Returns sourceFile.
     *
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }

    /**
     * Returns targetFile.
     *
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getTargetFile()
    {
        return $this->targetFile;
    }

    /**
     * Returns sourceProject.
     *
     * @return ProjectInterface
     */
    public function getSourceProject()
    {
        return $this->sourceProject;
    }

    /**
     * @param ProjectInterface $sourceProject
     */
    public function setSourceProject(ProjectInterface $sourceProject)
    {
        $this->sourceProject = $sourceProject;
    }

    /**
     * Returns targetProject.
     *
     * @return ProjectInterface
     */
    public function getTargetProject()
    {
        return $this->targetProject;
    }

    /**
     * @param ProjectInterface $targetProject
     */
    public function setTargetProject(ProjectInterface $targetProject)
    {
        $this->targetProject = $targetProject;
    }
}
