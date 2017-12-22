<?php
/**
 * @file
 * Contains lrackwitz\Para\Event\ApplyPatchEvent.php.
 */

namespace lrackwitz\Para\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ApplyPatchEvent.
 *
 * @package lrackwitz\Para\Event
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
     */
    public function __construct(string $patchContent)
    {
        $this->patchContent = $patchContent;
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
}
