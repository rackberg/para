<?php

namespace Para\Tests\Unit\Project;

use Para\Entity\ProjectInterface;
use Para\Project\ProjectArrayDecorator;
use PHPUnit\Framework\TestCase;

/**
 * Class ProjectArrayDecoratorTest.
 *
 * @package Para\Tests\Unit\Project
 */
class ProjectArrayDecoratorTest extends TestCase
{
    /**
     * Tests that an array representation of the project data will be returned.
     */
    public function testReturnsAnArrayRepresentationOfTheProjectData()
    {
        $name = 'my_project';
        $path = 'the/path/to/the/project';
        $foregroundColor = 10;
        $backgroundColor = 20;

        $project = $this->prophesize(ProjectInterface::class);
        $project->getName()->willReturn($name);
        $project->getPath()->willReturn($path);
        $project->getForegroundColor()->willReturn($foregroundColor);
        $project->getBackgroundColor()->willReturn($backgroundColor);

        $projectArrayDecorator = new ProjectArrayDecorator($project->reveal());

        $result = $projectArrayDecorator->asArray();

        $this->assertEquals([
            'name' => $name,
            'path' => $path,
            'foreground_color' => $foregroundColor,
            'background_color' => $backgroundColor,
        ], $result);
    }
}
