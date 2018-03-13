<?php

namespace Para\Configuration;

use Para\Dumper\DumperInterface;
use Para\Entity\GroupInterface;
use Para\Entity\ProjectInterface;
use Para\Exception\AddGroupException;
use Para\Exception\GroupNotFoundException;
use Para\Exception\ProjectNotFoundException;
use Para\Factory\GroupFactoryInterface;
use Para\Factory\ProjectFactoryInterface;
use Para\Parser\ParserInterface;

/**
 * Class GroupConfiguration
 *
 * @package Para\Configuration
 */
class GroupConfiguration extends AbstractConfiguration implements GroupConfigurationInterface
{
    /**
     * The configured groups.
     *
     * @var GroupInterface[]
     */
    private $groups;

    /**
     * The group factory.
     *
     * @var \Para\Factory\GroupFactoryInterface
     */
    private $groupFactory;

    /**
     * The project factory.
     *
     * @var ProjectFactoryInterface
     */
    private $projectFactory;

    /**
     * GroupConfiguration constructor.
     *
     * @param ParserInterface $parser The parser.
     * @param DumperInterface $dumper The dumper.
     * @param \Para\Factory\GroupFactoryInterface $groupFactory The group factory.
     * @param ProjectFactoryInterface $projectFactory The project factory.
     */
    public function __construct(
        ParserInterface $parser,
        DumperInterface $dumper,
        GroupFactoryInterface $groupFactory,
        ProjectFactoryInterface $projectFactory
    ) {
        parent::__construct($parser, $dumper);

        $this->groupFactory = $groupFactory;
        $this->projectFactory = $projectFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(GroupInterface $group): void
    {
        if (isset($this->groups[$group->getName()])) {
            throw new AddGroupException('The group to add is already configured');
        }
        $this->groups[$group->getName()] = $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup(string $groupName): void
    {
        if (!isset($this->groups[$groupName])) {
            throw new GroupNotFoundException(sprintf(
                'The group "%s" is not configured and could not be deleted from the configuration',
                $groupName
            ));
        }
        unset($this->groups[$groupName]);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup(string $groupName): ?GroupInterface
    {
        return isset($this->groups[$groupName]) ? $this->groups[$groupName] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $fileName): void
    {
        parent::load($fileName);

        foreach ($this->configuration['groups'] as $name => $value) {
            $group = $this->groupFactory->getGroup($name);
            $group->setProjects($value);
            $this->groups[$name] = $group;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(string $fileName): bool
    {
        unset($this->configuration['groups']);

        foreach ($this->groups as $group) {
            $this->configuration['groups'][$group->getName()] = $group->getProjects();
        }

        return parent::save($fileName);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProject(string $projectName): void
    {
        foreach ($this->groups as $groupName => $group) {
            if (array_key_exists($projectName, $group->getProjects())) {
                $group->removeProject($projectName);
                return;
            }
        }

        throw new ProjectNotFoundException($projectName);
    }

    /**
     * {@inheritdoc}
     */
    public function getProject(string $projectName): ?ProjectInterface
    {
        foreach ($this->groups as $group) {
            $projects = $group->getProjects();
            if (array_key_exists($projectName, $projects)) {
                $projectData = $projects[$projectName];
                $projectData['name'] = $projectName;
                return $this->projectFactory->getProjectFromArray($projectData);
            }
        }

        return null;
    }
}
