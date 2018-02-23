<?php
/**
 * @file
 * Contains Para\Service\Sync\FileSyncerInterface.php.
 */

namespace Para\Service\Sync;

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

    /**
     * Sets the git repository of the source file.
     *
     * @param string $gitRepository
     */
    public function setSourceGitRepository(string $gitRepository);

    /**
     * Sets the git repository of the target file.
     *
     * @param string $gitRepository
     */
    public function setTargetGitRepository(string $gitRepository);
}
