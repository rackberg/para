<?php

namespace Para\Tests\Unit\Factory;

use Para\Entity\ProjectInterface;
use Para\Factory\ProjectArrayDecoratorFactory;
use Para\Project\ProjectArrayDecorator;
use PHPUnit\Framework\TestCase;

/**
 * Class ProjectArrayDecoratorFactoryTest.
 *
 * @package Para\Tests\Unit\Factory
 */
class ProjectArrayDecoratorFactoryTest extends TestCase
{
    /**
     * Tests that the getArrayDecorator() method returns a new ProjectArrayDecorator instance.
     */
    public function testTheGetArrayDecoratorMethodReturnsANewProjectArrayDecoratorInstance()
    {
        $project = $this->prophesize(ProjectInterface::class);

        $factory = new ProjectArrayDecoratorFactory();

        $result = $factory->getArrayDecorator($project->reveal());

        $this->assertTrue($result instanceof ProjectArrayDecorator);
    }
}
