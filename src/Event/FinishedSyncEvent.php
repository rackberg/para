<?php
/**
 * @file
 * Contains Para\Event\FinishedSyncEvent.php.
 */

namespace Para\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class FinishedSyncEvent.
 *
 * @package Para\Event
 */
class FinishedSyncEvent extends Event
{
    const NAME = 'finished.sync';

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
     * The file handle to the patch file.
     *
     * @var resource
     */
    private $patchFile;

    /**
     * FinishedSyncEvent constructor.
     *
     * @param resource $patchFile
     *   The file handle to the patch file.
     * @param \Symfony\Component\HttpFoundation\File\File $sourceFile
     *   The source file.
     * @param \Symfony\Component\HttpFoundation\File\File $targetFile
     *   The target file.
     */
    public function __construct($patchFile, File $sourceFile, File $targetFile)
    {
        $this->patchFile = $patchFile;
        $this->sourceFile = $sourceFile;
        $this->targetFile = $targetFile;
    }

    /**
     * Returns patchFile.
     *
     * @return resource
     */
    public function getPatchFile()
    {
        return $this->patchFile;
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
}
