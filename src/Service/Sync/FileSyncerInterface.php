<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\Sync\FileSyncerInterface.php.
 */

namespace lrackwitz\Para\Service\Sync;

use Symfony\Component\HttpFoundation\File\File;

interface FileSyncerInterface
{
    /**
     * Syncs the source file with the target file.
     *
     * @param \Symfony\Component\HttpFoundation\File\File $sourceFile
     *   The source file containing the changes to sync.
     * @param \Symfony\Component\HttpFoundation\File\File $targetFile
     *   The target file that should be changed during the sync.
     *
     * @return bool
     *   True if the sync has been processed successfully, otherwise false.
     */
    public function sync(File $sourceFile, File $targetFile): bool;
}