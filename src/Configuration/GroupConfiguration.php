<?php

namespace Para\Configuration;

use Para\Entity\GroupInterface;
use Para\Exception\AddGroupException;
use Para\Exception\GroupNotFoundException;
use Para\Factory\GroupFactoryInterface;
use Para\Service\ConfigurationManagerInterface;

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
     * The group factory mock object.
     *
     * @var \Para\Factory\GroupFactoryInterface
     */
    private $groupFactory;

    /**
     * GroupConfiguration constructor.
     *
     * @param \Para\Service\ConfigurationManagerInterface $configurationManager The configuration manager.
     * @param \Para\Factory\GroupFactoryInterface $groupFactory The group factory.
     */
    public function __construct(
        ConfigurationManagerInterface $configurationManager,
        GroupFactoryInterface $groupFactory
    ) {
        parent::__construct($configurationManager);
        $this->groupFactory = $groupFactory;
        $this->addDefaultGroup();
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

    private function addDefaultGroup()
    {
        $this->groups['default'] = $this->groupFactory->getGroup('default');
    }
}
