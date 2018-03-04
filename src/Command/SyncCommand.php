<?php
/**
 * @file
 * Contains Para\Command\SyncCommand.php.
 */

namespace Para\Command;

use Para\Configuration\ProjectConfigurationInterface;
use Para\Service\Sync\FileSyncerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class SyncCommand.
 *
 * @package Para\Command
 */
class SyncCommand extends Command
{
    /**
     * The git file syncer.
     *
     * @var \Para\Service\Sync\FileSyncerInterface
     */
    private $fileSyncer;

    /**
     * The project configuration.
     *
     * @var \Para\Configuration\ProjectConfigurationInterface
     */
    private $projectConfiguration;

    /**
     * The file system.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;

    /**
     * SyncCommand constructor.
     *
     * @param \Para\Service\Sync\FileSyncerInterface $fileSyncer
     *   The git file syncer.
     * @param \Para\Configuration\ProjectConfigurationInterface $projectConfiguration
     *   The project configuration.
     * @param \Symfony\Component\Filesystem\Filesystem $fileSystem
     *   The file system.
     */
    public function __construct(
        FileSyncerInterface $fileSyncer,
        ProjectConfigurationInterface $projectConfiguration,
        Filesystem $fileSystem
    ) {
        parent::__construct();

        $this->fileSyncer = $fileSyncer;
        $this->projectConfiguration = $projectConfiguration;
        $this->fileSystem = $fileSystem;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sync')
            ->setDescription('Syncs changes from a file of a project to other projects.')
            ->setHelp('This command allows you to sync file changes made in a project with other projects you specify.')
            ->addArgument(
                'source_project',
                InputArgument::REQUIRED,
                'The name of the project that contains the file to sync.'
            )
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'The path relative to the source projects path ' .
                           'for the file that needs to be synced within other projects.'
            )
            ->addArgument(
                'target_project',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'One or more project names to sync the file with.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Abort execution when input data not valid.
        if (!$this->isInputValid($input, $output)) {
            return false;
        }

        // Get the target projects.
        $targetProjects = $input->getArgument('target_project');

        foreach ($targetProjects as $projectName) {
            $this->beginSync(
                $projectName,
                $input->getArgument('source_project'),
                $input->getArgument('file'),
                $output
            );
        }

        $output->writeln('Finished sync.');
    }

    /**
     * Begins syncing the source projects file to the target project.
     *
     * @param string $projectName
     *   The target project name.
     * @param string $sourceProjectName
     *   The source project name.
     * @param string $file
     *   The absolute path of the file to sync.
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *   The output.
     */
    private function beginSync(
        string $projectName,
        string $sourceProjectName,
        string $file,
        OutputInterface $output
    ) {
        // Get the path configured for the source project.
        $sourceProjectPath = $this->getProjectPath($sourceProjectName);
        $this->fileSyncer->setSourceGitRepository($sourceProjectPath);

        // Get the path configured for the project to sync.
        $projectPath = $this->getProjectPath($projectName);
        $this->fileSyncer->setTargetGitRepository($projectPath);

        $filePath = $sourceProjectPath{strlen($sourceProjectPath) - 1} != '/'
            ? $sourceProjectPath . '/' . $file
            : $sourceProjectPath . $file;

        $targetFilePath = $projectPath{strlen($projectPath) - 1} != '/'
            ? $projectPath . '/' . $file
            : $projectPath . $file;

        // Start the sync process.
        try {
            if (!$this->fileSyncer->sync(
                new File($filePath, false),
                new File($targetFilePath, false)
            )) {
                $output->writeln(
                    '<error>Failed to sync the file with project "'.$projectName.'"</error>',
                    1
                );
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     * Checks if the input is valid.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *   The input by the user.
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *   The output.
     *
     * @return bool
     *   Returns true if valid, otherwise false.
     */
    private function isInputValid(InputInterface $input, OutputInterface $output)
    {
        $project = $this->projectConfiguration->getProject(
            $input->getArgument('source_project')
        );

        // Check if the source project is configured.
        if (!$project) {
            $output->writeln('<error>The project you are trying to use as source_project is not configured.</error>', 1);
            return false;
        }

        // Get the source project path.
        $path = $project->getPath();
        if ($path{strlen($path) - 1} != '/') {
            $path .= '/';
        }

        // Check if the file to sync exists.
        if (!$this->fileSystem->exists($path . $input->getArgument('file'))) {
            $output->writeln('<error>The file you want to sync does not exist!</error>', 1);
            return false;
        }

        // Check if the target projects are configured.
        foreach ($input->getArgument('target_project') as $project) {
            if (!$this->projectConfiguration->getProject($project)) {
                $output->writeln('<error>The project "' . $project . '" you are trying to use as target_project is not configured.</error>', 1);
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the path of the project.
     *
     * @param string $projectName
     *   The name of the project.
     *
     * @return string
     *   The path of the project.
     */
    private function getProjectPath(string $projectName): string
    {
        $project = $this->projectConfiguration->getProject($projectName);
        return $project->getPath();
    }
}
