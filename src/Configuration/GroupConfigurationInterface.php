<?php

namespace Para\Configuration;

use Para\Entity\GroupInterface;

/**
 * Interface GroupConfigurationInterface.
 *
 * @package Para\Configuration
 */
interface GroupConfigurationInterface extends ConfigurationInterface
{
    /**
     * Adds a group to the configuration.
     *
     * @param \Para\Entity\GroupInterface $group The group to add.
     *
     * @throws \Para\Exception\AddGroupException When a group with the same name is already configured.
     */
    public function addGroup(GroupInterface $group): void;

    /**
     * Returns an array of configured groups.
     *
     * @return GroupInterface[]
     */
    public function getGroups(): array;

    /**
     * Deletes a group from the configuration.
     *
     * @param string $groupName The name of the group to delete.
     *
     * @throws \Para\Exception\GroupNotFoundException When a group to delete is not configured.
     */
    public function deleteGroup(string $groupName): void;

    /**
     * Returns a configured group instance for the requested group name.
     *
     * @param string $groupName The group name.
     *
     * @return null|\Para\Entity\GroupInterface The group instance or null.
     */
    public function getGroup(string $groupName): ?GroupInterface;
}
