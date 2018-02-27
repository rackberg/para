<?php
/**
 * @file
 * Contains Para\Tests\Service\YamlConfigurationManagerTest.php.
 */

namespace Para\Tests\Unit\Service;

use Para\Entity\Project;
use Para\Service\YamlConfigurationManager;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

/**
 * Class YamlConfigurationManagerTest.
 *
 * @package Para\Tests\Unit\Service
 */
class YamlConfigurationManagerTest extends TestCase
{
    /**
     * The yaml configuration manager to test.
     *
     * @var \Para\Service\YamlConfigurationManager
     */
    private $yamlConfigManager;

    private $vfsRoot;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->vfsRoot = vfsStream::setup();
        $this->createTestConfiguration();

        $this->yamlConfigManager = new YamlConfigurationManager(
            $this->prophesize(LoggerInterface::class)->reveal(),
            new Dumper(),
            new Parser(),
            vfsStream::url('root/para.yml')
        );
    }

    /**
     * Tests the findProjectByFile() method.
     *
     * @dataProvider findProjectByFileProvider
     *
     * @param string $file
     *   The path of the file.
     * @param $project
     *   The instance of the project.
     */
    public function testFindProjectByFile(string $file, $project)
    {
        $result = $this->yamlConfigManager->findProjectByFile(new File($file, false));
        $this->assertEquals($project, $result, 'Expected that the right project has been found.');
    }

    /**
     * Data provider for the findProjectByFile test.
     *
     * @return array
     *   The provided test data.
     */
    public function findProjectByFileProvider()
    {
        $project1 = new Project();
        $project1->setName('project_c');
        $project1->setRootDirectory('projects/project_c');

        $project2 = new Project();
        $project2->setName('project_a');
        $project2->setRootDirectory('projects/project_a');

        $project3 = new Project();
        $project3->setName('project_b');
        $project3->setRootDirectory('projects/project_b');

        return [
            [
                'file' => 'projects/project_c/my_file.txt',
                'project' => $project1,
            ],
            [
                'file' => 'projects/project_a/my_file.txt',
                'project' => $project2,
            ],
            [
                'file' => 'projects/project_b/my_file.txt',
                'project' => $project3,
            ],
            [
                'file' => 'projects/project_d/my_file.txt',
                'project' => null,
            ],
        ];
    }

    /**
     * Tests that the addGroup() method adds a new group entry into the
     */
    public function testTheAddGroupMethodAddsANewGroupIntoTheConfigurationFile()
    {
        $result = $this->yamlConfigManager->addGroup('new_group');

        $this->assertTrue($result);
    }

    /**
     * Tests that the addGroup() method returns false when the group to add already exists.
     */
    public function testTheAddGroupMethodReturnsFalseWhenTheGroupToAddAlreadyExists()
    {
        $result = $this->yamlConfigManager->addGroup('default');

        $this->assertFalse($result);
    }

    /**
     * Tests that the deleteGroup() method deletes an existing group from the configuration file.
     */
    public function testTheDeleteGroupMethodDeletesAnExistingGroupFromTheConfigurationFile()
    {
        $result = $this->yamlConfigManager->deleteGroup('default');

        $this->assertTrue($result);
    }

    /**
     * Tests that a GroupNotFoundException will be thrown when trying to delete a not existing group.
     *
     * @expectedException \Para\Exception\GroupNotFoundException
     */
    public function testTheDeleteGroupMethodThrowsAGroupNotFoundExceptionWhenTheGroupToDeleteDoesNotExist()
    {
        $this->yamlConfigManager->deleteGroup('not_existing_group');
    }

    private function createTestConfiguration()
    {
        vfsStream::newFile('para.yml')->at($this->vfsRoot)->setContent(
            <<< EOT
default:
    project_a: 
        path: projects/project_a
    project_b:
        path: projects/project_b
    project_c:
        path: projects/project_c
EOT
        );
    }
}