<?php

namespace Para\Tests\Unit\Factory;

use Para\Entity\ProjectInterface;
use Para\Factory\ProjectFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class ProjectFactoryTest
 *
 * @package Para\Tests\Unit\Factory
 */
class ProjectFactoryTest extends TestCase
{
    /**
     * Tests that the getProject() method returns the correct project instance.
     */
    public function testTheGetProjectMethodReturnsAProjectInstance()
    {
        $projectName = 'my_project';
        $path = 'the/project/path';
        $projectFactory = new ProjectFactory();
        $project = $projectFactory->getProject($projectName, $path);

        $this->assertTrue($project instanceof ProjectInterface);
        $this->assertEquals($projectName, $project->getName());
        $this->assertEquals($path, $project->getPath());
    }
}
