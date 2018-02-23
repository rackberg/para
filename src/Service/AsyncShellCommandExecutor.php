<?php
/**
 * @file
 * Contains Para\Service\AsyncShellCommandExecutor.php.
 */

namespace Para\Service;

use Para\Entity\Project;
use Para\Service\Output\BufferedOutputInterface;
use Para\Service\Strategy\AsyncShellCommandExecuteStrategy;
use Para\Service\Strategy\DisplayStrategyFactory;

/**
 * Class AsyncShellCommandExecutor.
 *
 * @package Para\Service
 */
class AsyncShellCommandExecutor
{
    /**
     * The factory to create display strategies.
     *
     * @var \Para\Service\Strategy\DisplayStrategyFactory
     */
    private $factory;

    /**
     * The strategy to use for command execution.
     *
     * @var Strategy\AsyncShellCommandExecuteStrategy
     */
    private $executeStrategy;

    /**
     * An array of already used color codes.
     *
     * @var array
     */
    private $usedColorCodes = [];

    /**
     * AsyncShellCommandExecutor constructor.
     *
     * @param \Para\Service\Strategy\DisplayStrategyFactory $factory The display strategy factory.
     */
    public function __construct(DisplayStrategyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Executes a shell command asynchronously in all registered projects.
     *
     * The output of every project will be shown as soon as possible.
     * For every project a log file will be created.
     *
     * @param string $cmd The shell command.
     * @param array $projects The array of projects.
     * @param \Para\Service\Output\BufferedOutputInterface $output The output buffer.
     */
    public function execute($cmd, array $projects, BufferedOutputInterface $output)
    {
        foreach ($projects as $name => $data) {
            $project = new Project();
            $project->setName($name);
            $project->setRootDirectory($data['path']);
            if (!empty($data['foreground_color'])) {
                $project->setForegroundColor($data['foreground_color']);
            } else {
                $project->setForegroundColor($this->getRandomColorCode());
            }
            if (!empty($data['background_color'])) {
                $project->setBackgroundColor($data['background_color']);
            }

            $projects[$name] = $project;
        }

        $this->executeStrategy = $this->factory->createCombinedOutputDisplayStrategy();
        $this->executeStrategy->execute($cmd, $projects, $output);
    }

    /**
     * Returns a random not yet used color code.
     *
     * @return int The random color code.
     */
    private function getRandomColorCode()
    {
        do {
            $colorCode = rand(0, 255);
        } while (in_array($colorCode, $this->usedColorCodes));

        // Add the color to the used color code.
        $this->usedColorCodes[] = $colorCode;

        return $colorCode;
    }

    /**
     * Sets the strategy.
     *
     * @param AsyncShellCommandExecuteStrategy $executeStrategy
     */
    public function setExecuteStrategy(
        AsyncShellCommandExecuteStrategy $executeStrategy
    ) {
        $this->executeStrategy = $executeStrategy;
    }

    /**
     * Returns executeStrategy.
     *
     * @return AsyncShellCommandExecuteStrategy
     */
    public function getExecuteStrategy()
    {
        return $this->executeStrategy;
    }
}
