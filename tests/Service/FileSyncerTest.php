<?php
/**
 * @file
 * Contains lrackwitz\Para\Tests\Service\FileSyncerTest.php.
 */

namespace lrackwitz\Para\Tests\Service;

use lrackwitz\Para\Service\Sync\FileSyncer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class FileSyncerTest.
 *
 * @package lrackwitz\Para\Tests\Service
 */
class FileSyncerTest extends TestCase
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
        $this->fileSyncer = new FileSyncer();

        $this->createTestDirectory();
    }

    /**
     * Tests the sync() method.
     *
     * @dataProvider dataProviderSync
     */
    public function testSync($sourceFile, $targetFile)
    {
        $sourceFile = new File($sourceFile, false);
        $targetFile = new File($targetFile, false);

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
                'targetFile' => self::TESTS_DIRECTORY . '/project_2/readme.md',
            ],
            'create config.yml' => [
                'sourceFile' => self::TESTS_DIRECTORY . '/project_1/app/config/config.yml',
                'targetFile' => self::TESTS_DIRECTORY . '/project_2/app/config/config.yml',
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
            self::TESTS_DIRECTORY . '/project_3'
        ]);

        // Files for project_1
        $fs->touch(self::TESTS_DIRECTORY . '/project_1/readme.md');
        $fs->appendToFile(self::TESTS_DIRECTORY . '/project_1/readme.md', 'Some text content');
        $fs->touch(self::TESTS_DIRECTORY . '/project_1/app/config/config.yml');
        $fs->appendToFile(self::TESTS_DIRECTORY . '/project_1/app/config/config.yml', 'parameters:' . "\n\t" . 'key: "value"');
        $fs->touch(self::TESTS_DIRECTORY . '/project_1/app/config/config.dev.yml');
        $fs->appendToFile(self::TESTS_DIRECTORY . '/project_1/app/config/config.dev.yml', 'some text content');

        // Files for project_2
        $fs->touch(self::TESTS_DIRECTORY . '/project_2/readme.md');
        $fs->appendToFile(self::TESTS_DIRECTORY . '/project_2/readme.md', 'Some content');
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
