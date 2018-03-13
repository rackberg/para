<?php

namespace Para\Entity;

/**
 * Class Group
 *
 * @package Para\Entity
 */
class Group implements GroupInterface
{
    /**
     * The name of the group.
     *
     * @var string
     */
    private $name;

    /**
     * The projects array.
     *
     * @var array
     */
    private $projects = [];

    /**
     * Group constructor.
     *
     * @param string $name The name of the group.
     */
    public function __construct(string $name = null)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function addProject(array $project): void
    {
        $this->projects[$project['name']] = $project;
    }

    /**
     * {@inheritdoc}
     */
    public function removeProject($projectName): void
    {
        unset($this->projects[$projectName]);
    }

    /**
     * {@inheritdoc}
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    /**
     * {@inheritdoc}
     */
    public function setProjects(array $projects): void
    {
        $this->projects = $projects;
    }
}
