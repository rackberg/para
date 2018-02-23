<?php
/**
 * @file
 * Contains Para\Event\CompareHunksEvent.php.
 */

namespace Para\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class CompareHunksEvent.
 *
 * @package Para\Event
 */
class CompareHunksEvent extends Event
{
    const NAME = 'para.event.compare_hunks';

    /**
     * The hunk used for the comparison only.
     *
     * @var string
     */
    private $compareHunk;

    /**
     * The hunk that will be modified.
     *
     * @var string
     */
    private $hunk;

    /**
     * The identifier of the comparison hunk.
     *
     * @var string
     */
    private $hunkIdentifier;

    /**
     * A flag to indicate if the compared hunks match.
     *
     * @var bool
     */
    private $isMatching = false;

    /**
     * CompareHunksEvent constructor.
     *
     * @param string $compareHunk
     *   The hunk used for comparison only.
     * @param string $hunk
     *   The hunk that will be modified.
     * @param string $hunkIdentifier
     *   The identifier of the comparison hunk.
     */
    public function __construct(string $compareHunk, string $hunk, string $hunkIdentifier)
    {
        $this->compareHunk = $compareHunk;
        $this->hunk = $hunk;
        $this->hunkIdentifier = $hunkIdentifier;
    }

    /**
     * Returns compareHunk.
     *
     * @return string
     */
    public function getCompareHunk()
    {
        return $this->compareHunk;
    }

    /**
     * Returns hunk.
     *
     * @return string
     */
    public function getHunk()
    {
        return $this->hunk;
    }

    /**
     * @param string $hunk
     */
    public function setHunk(string $hunk)
    {
        $this->hunk = $hunk;
    }

    /**
     * Returns hunkIdentifier.
     *
     * @return string
     */
    public function getHunkIdentifier()
    {
        return $this->hunkIdentifier;
    }

    /**
     * @param string $hunkIdentifier
     */
    public function setHunkIdentifier(string $hunkIdentifier)
    {
        $this->hunkIdentifier = $hunkIdentifier;
    }

    /**
     * Returns isMatching.
     *
     * @return bool
     */
    public function isMatching()
    {
        return $this->isMatching;
    }

    /**
     * @param bool $isMatching
     */
    public function setMatching(bool $isMatching)
    {
        $this->isMatching = $isMatching;
    }
}
