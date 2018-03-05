<?php

namespace Para\Factory;

use Para\Entity\Group;
use Para\Entity\GroupInterface;

/**
 * Class GroupFactory
 *
 * @package Para\Factory
 */
class GroupFactory implements GroupFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function getGroup(string $groupName): GroupInterface
    {
        $group = new Group();
        $group->setName($groupName);
        return $group;
    }
}
