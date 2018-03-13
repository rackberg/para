<?php

namespace Para\Factory;

use Para\Entity\Project;
use Para\Entity\ProjectInterface;

/**
 * Class ProjectFactory
 *
 * @package Para\Factory
 */
class ProjectFactory implements ProjectFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProject(
        string $projectName,
        string $path,
        string $foregroundColor = null,
        string $backgroundColor = null
    ): ProjectInterface {
        $project = new Project($projectName, $path);
        if ($foregroundColor) {
            $project->setForegroundColor($foregroundColor);
        }
        if ($backgroundColor) {
            $project->setBackgroundColor($backgroundColor);
        }

        return $project;
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectFromArray(array $projectData): ProjectInterface
    {
        return $this->getProject(
            $projectData['name'],
            $projectData['path'],
            isset($projectData['foreground_color']) ? $projectData['foreground_color'] : null,
            isset($projectData['background_color']) ? $projectData['background_color'] : null
        );
    }
}
