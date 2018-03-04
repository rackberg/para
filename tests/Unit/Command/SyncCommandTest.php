<?php

namespace Para\Tests\Unit\Command;

use Para\Command\SyncCommand;
use Para\Configuration\ProjectConfigurationInterface;
use Para\Entity\Project;
use Para\Service\Sync\GitFileSyncer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class SyncCommandTest.
 *
 * @package Para\Tests\Unit\Command
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
     * @var \Para\Service\Sync\FileSyncerInterface
     */
    private $gitFileSyncer;

    /**
     * The project configuration.
     *
     * @var \Para\Configuration\ProjectConfigurationInterface
     */
    private $projectConfiguration;

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
        $this->projectConfiguration = $this->prophesize(ProjectConfigurationInterface::class);
        $this->fileSystem = $this->prophesize(Filesystem::class);

        $this->application = new Application();
        $this->application->add(new SyncCommand(
            $this->gitFileSyncer->reveal(),
            $this->projectConfiguration->reveal(),
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

        $sourceProject = new Project();
        $sourceProject->setRootDirectory('path/to/source/project');

        $targetProject1 = new Project();
        $targetProject1->setRootDirectory('path/to/target/project1');

        $targetProject2 = new Project();
        $targetProject2->setRootDirectory('path/to/target/project2');

        $this->projectConfiguration
            ->getProject($parameters['source_project'])
            ->willReturn($sourceProject);

        $this->projectConfiguration
            ->getProject($parameters['target_project'][0])
            ->willReturn($targetProject1);

        $this->projectConfiguration
            ->getProject($parameters['target_project'][1])
            ->willReturn($targetProject2);

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

        // Simulate that the file could not be found.
        $this->fileSystem->exists(Argument::any())->willReturn(false);

        $sourceProject = new Project();
        $sourceProject->setName('source_project');

        $this->projectConfiguration
            ->getProject($parameters['source_project'])
            ->willReturn($sourceProject);

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

        $sourceProject = new Project();
        $sourceProject->setName('source_project');

        $this->projectConfiguration
            ->getProject($parameters['source_project'])
            ->willReturn($sourceProject);
        $this->projectConfiguration
            ->getProject($parameters['target_project'][0])
            ->willReturn(null);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $this->assertContains(
            'The project "' . $parameters['target_project'][0] . '" you are trying to use as target_project is not configured',
            $commandTester->getDisplay(),
            'Expected that an error message for the invalid target_project parameter will be shown.'
        );
    }
}
