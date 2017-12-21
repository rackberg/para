<?php
/**
 * @file
 * Contains lrackwitz\Para\Tests\Service\GitFileSyncerTest.php.
 */

namespace lrackwitz\Para\Tests\Service;

use lrackwitz\Para\Service\Sync\DefaultFileSyncer;
use lrackwitz\Para\Service\Sync\GitFileSyncer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Process;

/**
 * Class GitFileSyncerTest.
 *
 * @package lrackwitz\Para\Tests\Service
 */
class GitFileSyncerTest extends TestCase
{
    const TESTS_DIRECTORY = '/tmp/para_tests';

    /**
     * The file syncer to test.
     *
     * @var \lrackwitz\Para\Service\Sync\FileSyncerInterface
     */
    private $fileSyncer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->fileSyncer = new GitFileSyncer();

        $this->createTestDirectory();
    }

    /**
     * Tests the sync() method.
     *
     * @dataProvider dataProviderSync
     */
    public function testSync($sourceFile, $sourceContent, $targetFile)
    {
        file_put_contents($sourceFile, $sourceContent);

        $sourceFile = new File($sourceFile, false);
        $targetFile = new File($targetFile, false);

        $this->fileSyncer->setSourceGitRepository(self::TESTS_DIRECTORY . '/project_1');
        $this->fileSyncer->setTargetGitRepository(self::TESTS_DIRECTORY . '/project_2');
        $this->fileSyncer->sync($sourceFile, $targetFile);

        $this->assertEquals(
            file_get_contents($sourceFile),
            file_get_contents($targetFile),
            'Expected that the content of the source file has been synced to the target file.'
        );
    }

    /**
     * Data provider for the sync test.
     *
     * @return array
     *   The provided test data.
     */
    public function dataProviderSync()
    {
        return [
            'readme.md' => [
                'sourceFile' => self::TESTS_DIRECTORY . '/project_1/readme.md',
                'sourceContent' => 'this is the readme.md file',
                'targetFile' => self::TESTS_DIRECTORY . '/project_2/readme.md',
            ],
        ];
    }

    /**
     * Creates the test directory.
     */
    private function createTestDirectory()
    {
        // Make sure everything is clean.
        $this->deleteTestDirectory();

        $fs = new Filesystem();
        $fs->mkdir([
            self::TESTS_DIRECTORY,
            self::TESTS_DIRECTORY . '/project_1/app/config',
            self::TESTS_DIRECTORY . '/project_2/app/config',
        ]);

        $process = new Process('git init', self::TESTS_DIRECTORY . '/project_1');
        $process->run();
        $process = new Process('git init', self::TESTS_DIRECTORY . '/project_2');
        $process->run();

        // Files for project_1
        $fs->touch(self::TESTS_DIRECTORY . '/project_1/readme.md');
        $fs->appendToFile(self::TESTS_DIRECTORY . '/project_1/readme.md', 'Some text content');

        // Files for project_2
        $fs->touch(self::TESTS_DIRECTORY . '/project_2/readme.md');
        $fs->appendToFile(self::TESTS_DIRECTORY . '/project_2/readme.md', 'Some text content');

        $process = new Process('git add . && git commit -m "test"', self::TESTS_DIRECTORY . '/project_1');
        $process->run();
        $process = new Process('git add . && git commit -m "test"', self::TESTS_DIRECTORY . '/project_2');
        $process->run();
    }

    /**
     * Deletes the test directory.
     */
    private function deleteTestDirectory()
    {
        // Make sure the directory is not already existing.
        $fs = new Filesystem();
        $fs->remove(self::TESTS_DIRECTORY);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->deleteTestDirectory();
    }
}
