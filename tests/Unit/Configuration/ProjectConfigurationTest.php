<?php

namespace Para\Tests\Unit\Configuration;

use Para\Configuration\ProjectConfiguration;
use Para\Entity\Project;
use Para\Entity\ProjectInterface;
use Para\Factory\ProjectFactoryInterface;
use Para\Service\ConfigurationManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class ProjectConfigurationTest
 *
 * @package Para\Tests\Unit\Configuration
 */
class ProjectConfigurationTest extends TestCase
{
    /**
     * The project configuration to test.
     *
     * @var \Para\Configuration\ProjectConfigurationInterface
     */
    private $projectConfiguration;

    /**
     * The configuration manager mock object.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * The project factory.
     *
     * @var ProjectFactoryInterface
     */
    private $projectFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->configManager = $this->prophesize(ConfigurationManagerInterface::class);
        $this->projectFactory = $this->prophesize(ProjectFactoryInterface::class);

        $this->projectConfiguration = new ProjectConfiguration(
            $this->configManager->reveal(),
            $this->projectFactory->reveal()
        );
    }

    /**
     * Tests that the getProject() method returns the correct project instance.
     */
    public function testTheGetProjectMethodReturnsTheCorrectProjectInstance()
    {
        $this->configManager->getData()->willReturn([
            'default' => [
                'my_project' => [
                    'path' => 'the/path',
                ],
            ],
        ]);

        $projectName = 'my_project';
        $path = 'the/path';

        $project = new Project();
        $project->setName($projectName);
        $project->setRootDirectory($path);

        $this->projectFactory
            ->getProject($projectName, $path)
            ->willReturn($project);

        $result = $this->projectConfiguration->getProject($projectName);

        $this->assertTrue($result instanceof ProjectInterface);
        $this->assertEquals($projectName, $result->getName());
        $this->assertEquals($path, $result->getPath());
    }

    /**
     * Tests that the getProject() method returns null if the project to return is not configured.
     */
    public function testTheGetProjectMethodReturnsNullIfTheProjectIsNotConfigured()
    {
        $this->configManager->getData()->willReturn([
            'default' => [
                'a_project' => [],
            ],
        ]);
        $this->assertNull($this->projectConfiguration->getProject('not_existing_project'));
    }
}
