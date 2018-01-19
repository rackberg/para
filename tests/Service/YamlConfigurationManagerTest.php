<?php
/**
 * @file
 * Contains lrackwitz\Para\Tests\Service\YamlConfigurationManagerTest.php.
 */

namespace lrackwitz\Para\Tests\Service;

use lrackwitz\Para\Entity\Project;
use lrackwitz\Para\Service\YamlConfigurationManager;
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
 * @package lrackwitz\Para\Tests\Service
 */
class YamlConfigurationManagerTest extends TestCase
{
    /**
     * The yaml configuration manager to test.
     *
     * @var \lrackwitz\Para\Service\YamlConfigurationManager
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

    private function createTestConfiguration()
    {
        vfsStream::newFile('para.yml')->at($this->vfsRoot)->setContent(
            <<< EOT
default:
    project_a: projects/project_a
    project_b: projects/project_b
    project_c: projects/project_c
EOT
        );
    }
}
