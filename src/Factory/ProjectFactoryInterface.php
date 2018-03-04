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
     * Returns a new project instance.
     *
     * @param string $projectName The project name.
     * @param string $path The path.
     *
     * @return \Para\Entity\ProjectInterface The new project.
     */
    public function getProject(string $projectName, string $path): ProjectInterface;
}
