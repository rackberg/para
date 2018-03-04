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
     * @var \Para\Entity\Project[]
     */
    private $projects;

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
