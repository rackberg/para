<?php

namespace Para\Factory;

use Para\Entity\GroupInterface;

/**
 * Interface GroupFactoryInterface.
 *
 * @package Para\Factory
 */
interface GroupFactoryInterface
{
    /**
     * Returns a new group instance.
     *
     * @param string $groupName The name of the group.
     *
     * @return \Para\Entity\GroupInterface The new group instance.
     */
    public function getGroup(string $groupName): GroupInterface;
}
