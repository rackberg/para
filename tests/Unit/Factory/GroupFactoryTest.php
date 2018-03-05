<?php

namespace Para\Tests\Unit\Factory;

use Para\Entity\GroupInterface;
use Para\Factory\GroupFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class GroupFactoryTest
 *
 * @package Para\Tests\Unit\Factory
 */
class GroupFactoryTest extends TestCase
{
    /**
     * Tests that the getGroup() method returns an instance of a group.
     */
    public function testGetGroupMethodReturnsAnInstanceOfAGroup()
    {
        $groupFactory = new GroupFactory();
        $group = $groupFactory->getGroup('default');
        $this->assertTrue($group instanceof GroupInterface);
        $this->assertEquals('default', $group->getName());
    }
}
