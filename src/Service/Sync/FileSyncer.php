<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\Sync\FileSyncer.php.
 */

namespace lrackwitz\Para\Service\Sync;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class FileSyncer.
 *
 * @package lrackwitz\Para\Service\Sync
 */
class FileSyncer implements FileSyncerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sync(File $sourceFile, File $targetFile): bool
    {
        $patchFile = $this->createPatch($sourceFile, $targetFile);
        if ($this->hasContent($patchFile)) {
            $this->patchFile($targetFile, $patchFile);
            return true;
        } else {
            throw new \Exception('Nothing to sync. The files are identical.');
        }

        return false;
    }

    /**
     * Patches a file using a diff string.
     *
     * @param \Symfony\Component\HttpFoundation\File\File $file
     *   The file to patch.
     * @param resource $patchFile
     *   The handle to the patch file.
     *
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     *   When the patch process failed.
     */
    private function patchFile(File $file, $patchFile)
    {
        $patchFileMetaData = stream_get_meta_data($patchFile);

         // Construct the command to run.
        $command = sprintf(
            'patch -s --backup-if-mismatch %s %s',
            $file->getPathname(),
            $patchFileMetaData['uri']
        );
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * Creates a file diff using the os "diff" command.
     *
     * @param \Symfony\Component\HttpFoundation\File\File $file1
     *   The first file.
     * @param \Symfony\Component\HttpFoundation\File\File $file2
     *   The second file.
     *
     * @return resource
     *   The handle of the created patch file.
     */
    private function createPatch(File $file1, File $file2)
    {
        // Construct the command to run.
        $command = sprintf(
            'diff -u -a --new-file %s %s',
            $file2->getPathname(),
            $file1->getPathname()
        );

        // Run the process to create the diff.
        $process = new Process($command);
        $process->run();

        $diff = $process->getOutput();

        // Create temporary patch file.
        $patchFile = tmpfile();
        $metaData = stream_get_meta_data($patchFile);
        file_put_contents($metaData['uri'], $diff);

        return $patchFile;
    }

    /**
     * Checks if a file has content.
     *
     * @param resource $fileHandle
     *   The handle of the file.
     *
     * @return bool
     *   Returns true if the file has content otherwise false.
     */
    private function hasContent($fileHandle)
    {
        $metaData = stream_get_meta_data($fileHandle);
        $content = file_get_contents($metaData['uri']);
        return !empty($content);
    }
}
