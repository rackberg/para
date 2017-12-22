<?php
/**
 * @file
 * Contains lrackwitz\Para\Tests\Command\SyncCommandTest.php.
 */

namespace lrackwitz\Para\Tests\Command;

use lrackwitz\Para\Command\SyncCommand;
use lrackwitz\Para\Service\Sync\GitFileSyncer;
use lrackwitz\Para\Service\YamlConfigurationManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class SyncCommandTest.
 *
 * @package lrackwitz\Para\Tests\Command
 */
class SyncCommandTest extends TestCase
{
    /**
     * The application.
     *
     * @var Application
     */
    private $application;

    /**
     * The git file syncer.
     *
     * @var \lrackwitz\Para\Service\Sync\FileSyncerInterface
     */
    private $gitFileSyncer;

    /**
     * The config manager.
     *
     * @var \lrackwitz\Para\Service\ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * The file system.
     *
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->gitFileSyncer = $this->prophesize(GitFileSyncer::class);
        $this->configManager = $this->prophesize(YamlConfigurationManager::class);
        $this->fileSystem = $this->prophesize(Filesystem::class);

        $this->application = new Application();
        $this->application->add(new SyncCommand(
            $this->gitFileSyncer->reveal(),
            $this->configManager->reveal(),
            $this->fileSystem->reveal()
        ));
    }

    /**
     * Tests the successful exeuction of the sync command.
     */
    public function testExecuteIsSuccessful()
    {
        $command = $this->application->find('sync');
        $parameters = [
            'command' => $command->getName(),
            'source_project' => 'source_project',
            'file' => 'path/to/file.txt',
            'target_project' => ['target_project1', 'target_project2'],
        ];

        $this->simulateValidSourceAndTargetProjects(
            $parameters['source_project'],
            $parameters['target_project']
        );
        $this->configManager
            ->readProject($parameters['source_project'])
            ->willReturn('path/to/source/project');

        $this->configManager
            ->readProject($parameters['target_project'][0])
            ->willReturn('path/to/target/project1');

        $this->configManager
            ->readProject($parameters['target_project'][1])
            ->willReturn('path/to/target/project2');

        $this->fileSystem->exists(Argument::any())->willReturn(true);

        $this->gitFileSyncer
            ->setSourceGitRepository(Argument::type('string'))
            ->shouldBeCalled();

        $this->gitFileSyncer
            ->setTargetGitRepository(Argument::type('string'))
            ->shouldBeCalledTimes(2);

        $this->gitFileSyncer
            ->sync(Argument::any(), Argument::any())
            ->willReturn(true);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

//        $this->assertContains(
//            sprintf(
//                'Synced file "%s" of project "%s" to project',
//                $parameters['source_project'] . '/' . $parameters['file'],
//                $parameters['source_project']
//            ),
//            $output,
//            'Expected that the command output contains the message that the sync has been started.'
//        );

        $this->assertContains(
            'Finished sync',
            $output,
            'Expected that the command finished successfully.'
        );
    }

    /**
     * Tests that an error message will be shown when the source project is invalid.
     */
    public function testExecuteInvalidSourceProject()
    {
        $this->configManager->hasProject('source_project')->willReturn(false);

        $command = $this->application->find('sync');
        $parameters = [
            'command' => $command->getName(),
            'source_project' => 'source_project',
            'file' => 'path/to/file.txt',
            'target_project' => ['target_project1', 'target_project2'],
        ];

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $this->assertContains(
            'The project you are trying to use as source_project is not configured',
            $commandTester->getDisplay(),
            'Expected that an error message for the invalid source_project parameter will be shown.'
        );
    }

    /**
     * Tests that an error message will be shown when the file parameter is invalid.
     */
    public function testExecuteInvalidFile()
    {
        $command = $this->application->find('sync');
        $parameters = [
            'command' => $command->getName(),
            'source_project' => 'source_project',
            'file' => 'path/to/file.txt',
            'target_project' => ['target_project1', 'target_project2'],
        ];

        $this->simulateValidSourceAndTargetProjects(
            $parameters['source_project'],
            $parameters['target_project']
        );

        // Simulate that the file could not be found.
        $this->fileSystem->exists(Argument::any())->willReturn(false);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $this->assertContains(
            'The file you want to sync does not exist',
            $commandTester->getDisplay(),
            'Expected that an error message for the invalid file parameter will be shown.'
        );
    }

    /**
     * Tests that an error message will be shown when the target project is invalid.
     */
    public function testExecuteInvalidTargetProject()
    {
        $command = $this->application->find('sync');
        $parameters = [
            'command' => $command->getName(),
            'source_project' => 'source_project',
            'file' => 'path/to/file.txt',
            'target_project' => ['target_project1', 'target_project2'],
        ];

        $this->fileSystem->exists(Argument::any())->willReturn(true);

        $this->configManager->hasProject($parameters['source_project'])->willReturn(true);
        $this->configManager->hasProject($parameters['target_project'][0])->willReturn(false);

        $this->configManager->readProject($parameters['source_project'])->willReturn('path/to/source_project');

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $this->assertContains(
            'The project "' . $parameters['target_project'][0] . '" you are trying to use as target_project is not configured',
            $commandTester->getDisplay(),
            'Expected that an error message for the invalid target_project parameter will be shown.'
        );
    }

    /**
     * Simulates that the source and target projects are valid.
     *
     * @param string $sourceProject
     *   The name of the source project.
     * @param array $targetProjects
     *   The names of the target projects.
     */
    private function simulateValidSourceAndTargetProjects(string $sourceProject, array $targetProjects)
    {
        $this->configManager->hasProject($sourceProject)->willReturn(true);
        foreach ($targetProjects as $targetProject) {
            $this->configManager->hasProject($targetProject)->willReturn(true);
        }

        $this->configManager->readProject(Argument::type('string'))->willReturn('path/to/project');
    }
}
