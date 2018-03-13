<?php

namespace Para\Factory;

use Para\Entity\ProjectInterface;

/**
 * Interface ProjectFactoryInterface.
 *
 * @package Para\Factory
 */
interface ProjectFactoryInterface
{
    /**
     * Creates a new project instance.
     *
     * @param string $projectName The project name.
     * @param string $path The path.
     * @param string $foregroundColor The foreground color.
     * @param string $backgroundColor The background color.
     *
     * @return \Para\Entity\ProjectInterface The created project.
     */
    public function getProject(
        string $projectName,
        string $path,
        string $foregroundColor = null,
        string $backgroundColor = null
    ): ProjectInterface;

    /**
     * Creates a new project from an array containing project data.
     *
     * @param array $projectData The project data containing array.
     *
     * @return ProjectInterface The created project.
     */
    public function getProjectFromArray(array $projectData): ProjectInterface;
}
