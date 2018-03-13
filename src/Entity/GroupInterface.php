<?php

namespace Para\Entity;

/**
 * Interface GroupInterface.
 *
 * @package Para\Entity
 */
interface GroupInterface extends EntityInterface
{
    /**
     * Returns the name of the group.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Sets the name of the group.
     *
     * @param string $name The name of the group.
     */
    public function setName(string $name): void;

    /**
     * Adds a project.
     *
     * @param array $project The project data to add.
     */
    public function addProject(array $project): void;

    /**
     * Removes a project.
     *
     * @param string $projectName The name of the project to remove.
     */
    public function removeProject($projectName): void;

    /**
     * Returns an array of projects.
     *
     * @return \Para\Entity\Project[]
     */
    public function getProjects(): array;

    /**
     * Sets the projects.
     *
     * @param array $projects The projects.
     */
    public function setProjects(array $projects): void;
}
