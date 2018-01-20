<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\Sync\GitFileSyncer.php.
 */

namespace lrackwitz\Para\Service\Sync;

use Exception;
use lrackwitz\Para\Event\ApplyPatchEvent;
use lrackwitz\Para\Event\CompareHunksEvent;
use lrackwitz\Para\Event\FinishedCopyEvent;
use lrackwitz\Para\Event\FinishedSyncEvent;
use lrackwitz\Para\Event\HunksNotMatchingEvent;
use lrackwitz\Para\Event\StartSyncEvent;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class GitFileSyncer.
 *
 * @package lrackwitz\Para\Service\Sync
 */
class GitFileSyncer implements FileSyncerInterface
{

    const SPLIT_HUNKS_PATTERN = '~^(@@ .*? @@).*$~m';

    /**
     * The path to the local cloned git repository of the source file.
     *
     * @var string
     */
    private $sourceGitRepository;

    /**
     * The path to the local cloned git repository of the target file.
     *
     * @var string
     */
    private $targetGitRepository;

    /**
     * The event dispatcher.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * GitFileSyncer constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     *   The event dispatcher.
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function sync(File $sourceFile, File $targetFile): bool
    {
        // Check if we only need to copy the file to the target directory.
        if (!file_exists($targetFile)) {
            // Copy the source file to the target directory.
            $fs = new Filesystem();
            $fs->copy($sourceFile, $targetFile, true);

            $finishedCopyEvent = new FinishedCopyEvent($sourceFile, $targetFile);
            $this->dispatcher->dispatch(FinishedCopyEvent::NAME, $finishedCopyEvent);

            return true;
        }

        $startSyncEvent = new StartSyncEvent($sourceFile, $targetFile);
        $this->dispatcher->dispatch(StartSyncEvent::NAME, $startSyncEvent);

        // Execute the file sync.
        $patchFile = $this->createPatch($sourceFile);
        if ($this->hasContent($patchFile)) {
            if ($this->applyPatch($patchFile, $sourceFile, $targetFile)) {
                // Dispatch an event.
                $finishedSyncEvent = new FinishedSyncEvent($patchFile, $sourceFile, $targetFile);
                $this->dispatcher->dispatch(FinishedSyncEvent::NAME, $finishedSyncEvent);
            }
            return true;
        } else {
            throw new Exception('Nothing to sync. The files are identical.');
        }
    }

    /**
     * Creates a patch file.
     *
     * @param \Symfony\Component\HttpFoundation\File\File $file
     *   The file to create the patch from.
     *
     * @return resource
     *   The handle of the created patch file.
     */
    private function createPatch(File $file)
    {
        $patchFile = $this->createTemporaryFile();
        $metaData = stream_get_meta_data($patchFile);

        // Construct the command to run.
        $command = sprintf(
            'git diff --no-ext-diff --ignore-all-space %s > %s',
            $file,
            $metaData['uri']
        );

        $this->runCommand($command, false, $this->sourceGitRepository);

        return $patchFile;
    }

    /**
     * Applies a patch file.
     *
     * @param resource $patchFile
     *   The file handle of the patch file.
     * @param string $sourceFile
     *   The path of the source file.
     * @param $targetFile
     *   The path of the target file.
     *
     * @return bool
     *   Returns true if the patch could be applied, otherwise false.
     *
     * @throws \Exception
     */
    private function applyPatch($patchFile, $sourceFile, $targetFile)
    {
        $fs = new Filesystem();

        // 1. Overwrite the target file with the source file.
        $fs->copy($sourceFile, $targetFile, true);

        // 2. Detect the hunks of the changes.
        $hunks = $this->detectHunks($patchFile, $targetFile);

        // 3. Undo the copy.
        $this->runCommand(
            sprintf(
                'git checkout %s',
                $targetFile->getFilename()
            ),
            false,
            $this->targetGitRepository
        );

        // 4. Apply the hunks.
        $result = $this->applyHunks($hunks);

        return $result;
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

    /**
     * Runs a command in a single process.
     *
     * @param string $command
     *   The command to run.
     * @param bool $checkFail
     *   (Optional) Checks if the process execution failed.
     * @param string $cwd
     *   (Optional) The current working directory of the process.
     *
     * @return array
     *   An array containing stdout and stderr output of the process.
     */
    private function runCommand(string $command, bool $checkFail = true, $cwd = null): array
    {
        $process = new Process($command, $cwd);
        $process->run();

        if ($checkFail && !$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return [
            'stdout' => $process->getOutput(),
            'stderr' => $process->getErrorOutput(),
        ];
    }

    /**
     * Creates a temporary file with content and returns the file handle.
     *
     * @param string $content
     *   The content to apply to the temporary file.
     *
     * @return bool|resource
     *   Returns the file handle or false.
     */
    private function createTemporaryFile(string $content = null)
    {
        $tmpFile = tmpfile();
        if ($content) {
            $metaData = stream_get_meta_data($tmpFile);
            file_put_contents($metaData['uri'], $content);
        }

        return $tmpFile;
    }

    /**
     * Detects patch hunks.
     *
     * @param resource $patchFileHandle
     *   The patch file handle.
     * @param string $targetFilePath
     *   The path to the target file.
     *
     * @return array
     *   An array of detected hunks.
     *
     * @throws \Exception
     *   When an error occurred saving the temporary patch file.
     */
    private function detectHunks($patchFileHandle, string $targetFilePath): array
    {
        $command = sprintf(
            'git diff --ignore-all-space %s',
            $targetFilePath
        );
        $output = $this->runCommand($command, false, $this->targetGitRepository);

        $tmpDiff = $output['stdout'];
        if (empty($tmpDiff)) {
            return [];
        }

        // Split the patch file into hunks.
        $patchContent = $this->readContentOfFile($patchFileHandle);
        if (!$patchContent) {
            throw new Exception('Failed to read the content of temporary created patch file.');
        }
        $patchHunks = preg_split(self::SPLIT_HUNKS_PATTERN, $patchContent);
        $maxPatchHunks = count($patchHunks);

        // Split the output of the git diff command into hunks.
        $tmpHunks = preg_split(self::SPLIT_HUNKS_PATTERN, $tmpDiff, -1, PREG_SPLIT_DELIM_CAPTURE);

        // The first hunk is normally the header information.
        $header = array_shift($tmpHunks);

        // Look over the hunks and compare them with the patch file.
        $hunks = [];
        foreach ($tmpHunks as $key => $hunk) {
            // Ignore hunk headers.
            if ($key % 2 == 0) {
                continue;
            }

            for ($i = 1; $i < $maxPatchHunks; $i++) {
                // Trigger an event to compare the hunks.
                $event = new CompareHunksEvent($hunk, $patchHunks[$i], $tmpHunks[$key - 1]);
                $this->dispatcher->dispatch(CompareHunksEvent::NAME, $event);

                // Forget all hunks that do not match.
                if ($event->isMatching()) {
                    // Adds the compared hunk.
                    $hunks[] = $event->getHunkIdentifier() . $event->getHunk();
                }
            }
        }

        // Add the header.
        array_unshift($hunks, $header);

        return $hunks;
    }

    /**
     * Applies all hunks to the target repository.
     *
     * @param array $hunks
     *   The array of hunks.
     *
     * @return bool
     *   Returns true or false.
     */
    private function applyHunks(array $hunks): bool
    {
        // Create a temporary patch file.
        $patchContent = implode('', $hunks);

        $event = new ApplyPatchEvent($patchContent, $this->targetGitRepository);
        $this->dispatcher->dispatch(ApplyPatchEvent::NAME, $event);

        if ($event->isApproved()) {
            $patchFileHandle = $this->createTemporaryFile($patchContent);
            $patchFileMetaData = stream_get_meta_data($patchFileHandle);

            $command = sprintf(
                'git apply %s',
                $patchFileMetaData['uri']
            );
            $this->runCommand($command, true, $this->targetGitRepository);
            return true;
        }

        return false;
    }

    /**
     * Reads the content of a file by it's handle.
     *
     * @param resource $fileHandle
     *   The file handle.
     *
     * @return bool|string
     *   Returns the content or false.
     */
    private function readContentOfFile($fileHandle)
    {
        return file_get_contents(stream_get_meta_data($fileHandle)['uri']);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceGitRepository(string $gitRepository)
    {
        $this->sourceGitRepository = $gitRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetGitRepository(string $gitRepository)
    {
        $this->targetGitRepository = $gitRepository;
    }
}
