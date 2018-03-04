<?php

namespace Para\Entity;

/**
 * Interface GroupInterface.
 *
 * @package Para\Entity
 */
interface GroupInterface
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
