<?php

namespace Para\Tests\Unit\Factory;

use Para\Entity\ProjectInterface;
use Para\Factory\ProjectFactory;
use Para\Factory\ProjectFactoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class ProjectFactoryTest
 *
 * @package Para\Tests\Unit\Factory
 */
class ProjectFactoryTest extends TestCase
{
    /**
     * The project factory to test.
     *
     * @var ProjectFactoryInterface
     */
    private $projectFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->projectFactory = new ProjectFactory();
    }

    /**
     * Tests that the getProject() method returns the correct project instance.
     */
    public function testTheGetProjectMethodReturnsAProjectInstance()
    {
        $projectName = 'my_project';
        $path = 'the/project/path';
        $project = $this->projectFactory->getProject($projectName, $path);

        $this->assertTrue($project instanceof ProjectInterface);
        $this->assertEquals($projectName, $project->getName());
        $this->assertEquals($path, $project->getPath());
    }

    public function testTheGetProjectFromArrayMethodReturnsAProjectInstance()
    {
        $projectName = 'my_project';
        $path = 'the/project/path';
        $project = $this->projectFactory->getProjectFromArray([
            'name' => $projectName,
            'path' => $path,
        ]);

        $this->assertTrue($project instanceof ProjectInterface);
        $this->assertEquals($projectName, $project->getName());
        $this->assertEquals($path, $project->getPath());
    }
}
