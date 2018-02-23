<?php
/**
 * @file
 * Contains Para\Event\ApplyPatchEvent.php.
 */

namespace Para\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ApplyPatchEvent.
 *
 * @package Para\Event
 */
class ApplyPatchEvent extends Event
{
    const NAME = 'para.event.apply_patch';

    /**
     * The patch content.
     *
     * @var string
     */
    private $patchContent;

    /**
     * The path to the target file.
     *
     * @var string
     */
    private $targetPath;

    /**
     * The project.
     *
     * @var \Para\Entity\ProjectInterface
     */
    private $project;

    /**
     * The flag indicating if the patch apply has been approved.
     *
     * @var bool
     */
    private $approved = false;

    /**
     * ApplyPatchEvent constructor.
     *
     * @param string $patchContent
     *   The patch content.
     * @param string $targetPath
     *   The path to the target file.
     */
    public function __construct(string $patchContent, string $targetPath)
    {
        $this->patchContent = $patchContent;
        $this->targetPath = $targetPath;
    }

    /**
     * Returns patchContent.
     *
     * @return string
     */
    public function getPatchContent()
    {
        return $this->patchContent;
    }

    /**
     * Returns project.
     *
     * @return \Para\Entity\ProjectInterface
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Para\Entity\ProjectInterface $project
     */
    public function setProject(\Para\Entity\ProjectInterface $project)
    {
        $this->project = $project;
    }

    /**
     * Returns approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     */
    public function setApproved(bool $approved)
    {
        $this->approved = $approved;
    }

    /**
     * Returns targetPath.
     *
     * @return string
     */
    public function getTargetPath()
    {
        return $this->targetPath;
    }

    /**
     * @param string $targetPath
     */
    public function setTargetPath(string $targetPath)
    {
        $this->targetPath = $targetPath;
    }
}
