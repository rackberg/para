<?php

namespace Para\Configuration;

use Para\Entity\ProjectInterface;

/**
 * Interface ProjectConfigurationInterface.
 *
 * @package Para\Configuration
 */
interface ProjectConfigurationInterface extends ConfigurationInterface
{
    /**
     * Returns the project instance if the project is configured.
     *
     * @param string $projectName The project name.
     *
     * @return null|\Para\Entity\ProjectInterface The project instance or null.
     */
    public function getProject(string $projectName): ?ProjectInterface;
}
