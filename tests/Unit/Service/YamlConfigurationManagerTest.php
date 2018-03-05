<?php

namespace Para\Tests\Unit\Service;

use Para\Dumper\DumperInterface;
use Para\Dumper\YamlDumper;
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
    const TEST_CONTENT =
        <<< EOT
default:
    project_a: 
        path: projects/project_a
    project_b:
        path: projects/project_b
    project_c:
        path: projects/project_c
EOT;

    /**
     * The yaml configuration manager to test.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $yamlConfigManager;

    private $vfsRoot;

    /**
     * The parser mock object.
     *
     * @var \Symfony\Component\Yaml\Parser
     */
    private $parser;

    /**
     * The dumper mock object.
     *
     * @var DumperInterface
     */
    private $dumper;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->vfsRoot = vfsStream::setup();
        $this->createTestConfiguration();

        $this->parser = $this->prophesize(Parser::class);
        $this->parser
            ->parse(Argument::type('string'))
            ->willReturn([
                'default' => [
                    'project_a' => [
                        'path' => 'projects/project_a',
                    ],
                    'project_b' => [
                        'path' => 'projects/project_b',
                    ],
                    'project_c' => [
                        'path' => 'projects/project_c',
                    ],
                ],
            ]);

        $this->dumper = $this->prophesize(DumperInterface::class);

        $this->yamlConfigManager = new YamlConfigurationManager(
            $this->prophesize(LoggerInterface::class)->reveal(),
            $this->dumper->reveal(),
            $this->parser->reveal(),
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
     * Tests that the hasProject() method returns true, when the project exists in the configuration file.
     */
    public function testTheMethodHasProjectReturnsTrueWhenTheProjectExistsInTheConfigurationFile()
    {
        $this->assertTrue($this->yamlConfigManager->hasProject('project_a'));
    }

    /**
     * Tests that the hasProject() method returns false, when the project does not exist in the configuration file.
     */
    public function testTheMethodHasProjectReturnsFalseWhenTheProjectDoesNotExistInTheConfigurationFile()
    {
        $this->assertFalse($this->yamlConfigManager->hasProject('not_existing_project'));
    }

    /**
     * Tests that the readGroup() method returns an array.
     */
    public function testTheMethodReadGroupReturnsTheGroupAsArray()
    {
        $result = $this->yamlConfigManager->readGroup('default');

        $this->assertTrue(is_array($result));
    }

    /**
     * Tests that the readGroup() method throws a GroupNotFoundException.
     *
     * @expectedException \Para\Exception\GroupNotFoundException
     */
    public function testTheMethodReadGroupThrowsAGroupNotFoundExceptionWhenTheGroupIsNotExistingInTheConfigurationFile()
    {
        $this->yamlConfigManager->readGroup('not_existing_group');
    }

    /**
     * Tests that the readProject() method returns an array with the project data.
     */
    public function testTheReadProjectMethodReturnsAnArrayWithProjectData()
    {
        $result = $this->yamlConfigManager->readProject('project_a');

        $this->assertArrayHasKey('path', $result);
    }

    /**
     * Tests that the readProject() method returns null when the project doeas not exist.
     */
    public function testTheReadProjectMethodReturnsNullIfTheProjectDoesNotExist()
    {
        $this->assertNull($this->yamlConfigManager->readProject('not_existing'));
    }

    /**
     * Tests that the save() method saves the configuration.
     */
    public function testTheSaveMethodSavesTheConfiguration()
    {
        $fileName = 'vfs://root/para.yml';
        $content = 'test';
        $result = $this->yamlConfigManager->save($content);

        $this->assertTrue(file_exists($fileName));
        $this->assertTrue($result);
        $this->assertEquals($content, file_get_contents($fileName));
    }

    /**
     * Tests that the read() method reads the content of a yml file.
     */
    public function testTheReadMethodReadsTheContentOfAYmlFile()
    {
        $fileName = vfsStream::url('root/para.yml');
        $this->yamlConfigManager->read($fileName);

        $data = $this->yamlConfigManager->getData();
        $this->assertNotNull($data);
    }

    /**
     * Tests that the getData() method returns an array of data.
     */
    public function testTheGetDataMethodReturnsAnArrayWithData()
    {
        $this->yamlConfigManager->read('vfs://root/para.yml');
        $data = $this->yamlConfigManager->getData();

        $this->assertTrue(is_array($data));
    }

    /**
     * Tests that the getDumper() method returns the dumper.
     */
    public function testTheGetDumperMethodReturnsTheYamlDumper()
    {
        $dumper = $this->yamlConfigManager->getDumper();
        $this->assertTrue($dumper instanceof DumperInterface);
    }

    private function createTestConfiguration()
    {
        vfsStream::newFile('para.yml')
            ->at($this->vfsRoot)
            ->setContent(self::TEST_CONTENT);
    }
}
