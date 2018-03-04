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
    public function getProject(string $projectName, string $path): ProjectInterface
    {
        $project = new Project();
        $project->setName($projectName);
        $project->setRootDirectory($path);

        return $project;
    }
}
