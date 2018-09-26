<?php

namespace Para\Tests\Unit\Plugin;

use org\bovigo\vfs\vfsStream;
use Para\Factory\Encoder\JsonEncoderFactoryInterface;
use Para\Factory\PluginFactoryInterface;
use Para\Factory\ProcessFactoryInterface;
use Para\Package\ComposerPackageInterface;
use Para\Package\PackageFinderInterface;
use Para\Plugin\PluginInterface;
use Para\Plugin\PluginManager;
use Para\Service\Packagist\PackagistInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Process\Process;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class PluginManagerTest
 *
 * @package Para\Tests\Unit\Plugin
 */
class PluginManagerTest extends TestCase
{
    /**
     * The plugin manager to test.
     *
     * @var \Para\Plugin\PluginManagerInterface
     */
    private $pluginManager;

    /**
     * The plugin factory mock object.
     *
     * @var \Para\Factory\PluginFactoryInterface
     */
    private $pluginFactory;

    /**
     * The process factory mock object.
     *
     * @var \Para\Factory\ProcessFactoryInterface
     */
    private $processFactory;

    /**
     * The json encoder factory mock object.
     *
     * @var \Para\Factory\Encoder\JsonEncoderFactoryInterface
     */
    private $jsonEncoderFactory;

    /**
     * The package finder mock object.
     *
     * @var \Para\Package\PackageFinderInterface
     */
    private $packageFinder;

    /**
     * The packagist service.
     *
     * @var PackagistInterface
     */
    private $packagist;

    /**
     * A virtual file system.
     *
     * @var \org\bovigo\vfs\vfsStream
     */
    private $fileSystem;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        // Create a virtual file system with an empty composer.lock file.
        $directory = [
            'composer.lock' => '',
        ];
        $this->fileSystem = vfsStream::setup('root', 444, $directory);

        $this->pluginFactory = $this->prophesize(PluginFactoryInterface::class);
        $this->processFactory = $this->prophesize(ProcessFactoryInterface::class);
        $this->jsonEncoderFactory = $this->prophesize(JsonEncoderFactoryInterface::class);

        $this->packageFinder = $this->prophesize(PackageFinderInterface::class);
        $this->packagist = $this->prophesize(PackagistInterface::class);

        $this->pluginManager = new PluginManager(
            $this->pluginFactory->reveal(),
            $this->processFactory->reveal(),
            $this->jsonEncoderFactory->reveal(),
            $this->packageFinder->reveal(),
            $this->packagist->reveal(),
            $this->fileSystem->url() . '/'
        );
    }

    /**
     * Tests that the fetchPluginsAvailable() method returns an array of available plugins.
     */
    public function testTheMethodFetchPluginsAvailableReturnsAnArrayOfAvailablePlugins()
    {
        /** @var ComposerPackageInterface $package1 */
        $package1 = $this->prophesize(ComposerPackageInterface::class);
        $package1->getName()->willReturn('lrackwitz/para-alias');
        $package1->getVersion()->willReturn('1.0.0');
        $package1->getDescription()->willReturn('Some description');

        /** @var ComposerPackageInterface $package2 */
        $package2 = $this->prophesize(ComposerPackageInterface::class);
        $package2->getName()->willReturn('lrackwitz/para-sync');
        $package2->getVersion()->willReturn('dev-master');
        $package2->getDescription()->willReturn('Some description');

        $this->packagist
            ->findPackagesByType('para-plugin')
            ->willReturn([$package1->reveal(), $package2->reveal()]);

        /** @var PluginInterface $plugin1 */
        $plugin1 = $this->prophesize(PluginInterface::class);
        $plugin1->getName()->willReturn('lrackwitz/para-alias');
        $plugin1->setVersion('1.0.0')->shouldBeCalled();
        $plugin1->setDescription('Some description')->shouldBeCalled();

        /** @var PluginInterface $plugin2 */
        $plugin2 = $this->prophesize(PluginInterface::class);
        $plugin2->getName()->willReturn('lrackwitz/para-sync');
        $plugin2->setVersion('dev-master')->shouldBeCalled();
        $plugin2->setDescription('Some description')->shouldBeCalled();

        $this->pluginFactory->getPlugin('lrackwitz/para-alias')->willReturn($plugin1->reveal());
        $this->pluginFactory->getPlugin('lrackwitz/para-sync')->willReturn($plugin2->reveal());

        $plugins = $this->pluginManager->fetchPluginsAvailable();

        $this->assertTrue(is_array($plugins), 'Expected that an array has been returned');
        $this->assertEquals($plugins[0]->getName(), 'lrackwitz/para-alias', 'Expected that the first plugin is correct.');
        $this->assertEquals($plugins[1]->getName(), 'lrackwitz/para-sync', 'Expected that the second plugin is correct.');
    }

    /**
     * Tests that a plugin installs a plugin successfully.
     */
    public function testInstallsAPluginWithVersionDiscoverySuccessfully()
    {
        $pluginName = 'lrackwitz/para-alias';

        $versions = [
            '1.0.0',
            '1.1.0',
            '2.0.0',
            'dev-master',
        ];
        $this->packagist->getPackageVersions($pluginName)->willReturn($versions);
        $this->packagist->getHighestVersion($versions)->willReturn('2.0.0');

        $commandline = 'composer require lrackwitz/para-alias 2.0.0';

        $this->getIsInstalledJsonDecoder('something');

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled();
        $process->isSuccessful()->willReturn(true);
        $process->getOutput()->shouldBeCalled();
        $process->getOutput()->willReturn('something');

        $this->processFactory
            ->getProcess($commandline, $this->fileSystem->url() . '/')
            ->shouldBeCalled();
        $this->processFactory
            ->getProcess($commandline, $this->fileSystem->url() . '/')
            ->willReturn($process->reveal());

        $this->pluginManager->installPlugin($pluginName);
    }

    /**
     * Tests that installing a plugin fails.
     *
     * @expectedException \Para\Exception\PluginNotFoundException
     */
    public function testInstallingAPluginFails()
    {
        $pluginName = 'lrackwitz/para-alias';
        $pluginVersion = 'dev-master';

        $commandline = 'composer require lrackwitz/para-alias dev-master';

        $this->getIsInstalledJsonDecoder('something');

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled();
        $process->isSuccessful()->willReturn(false);

        $this->processFactory
            ->getProcess($commandline, $this->fileSystem->url() . '/')
            ->shouldBeCalled();
        $this->processFactory
            ->getProcess($commandline, $this->fileSystem->url() . '/')
            ->willReturn($process->reveal());

        $this->pluginManager->installPlugin($pluginName, $pluginVersion);
    }

    /**
     * Tests that the isInstalled() method returns true when the plugin is already installed.
     */
    public function testTheIsInstalledMethodReturnsTrueWhenThePluginIsAlreadyInstalled()
    {
        $pluginName = 'lrackwitz/para-alias';

        $jsonEncoder = $this->getIsInstalledJsonDecoder($pluginName);

        $result = $this->pluginManager->isInstalled($pluginName);

        $jsonEncoder->decode(Argument::type('string'), JsonEncoder::FORMAT)->shouldHaveBeenCalled();

        $this->jsonEncoderFactory->getEncoder()->shouldHaveBeenCalled();

        $this->assertTrue($result, 'Expected that the plugin has been found.');
    }

    /**
     * Tests that the uninstallPlugin() method throws an exception when the plugin is not installed.
     *
     * @expectedException \Para\Exception\PluginNotFoundException
     */
    public function testTheUninstallThrowsAnExceptionWhenThePluginIsNotInstalled()
    {
        $pluginName = 'lrackwitz/para-alias';

        $this->getIsInstalledJsonDecoder('something');

        $this->pluginManager->uninstallPlugin($pluginName);
    }

    /**
     * The that the uninstallPlugin() method is successful.
     */
    public function testThePluginUninstallIsSuccessful()
    {
        $pluginName = 'lrackwitz/para-alias';

        $commandline = 'composer remove lrackwitz/para-alias';

        $this->getIsInstalledJsonDecoder($pluginName);

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled();
        $process->isSuccessful()->willReturn(true);

        $this->processFactory->getProcess($commandline, $this->fileSystem->url() . '/')->shouldBeCalled();
        $this->processFactory->getProcess($commandline, $this->fileSystem->url() . '/')->willReturn($process->reveal());

        $this->pluginManager->uninstallPlugin($pluginName);
    }

    /**
     * Tests that the uninstallPlugin() method throws an exception when it fails unexpectedly.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Failed to uninstall the plugin.
     */
    public function testThePluginUninstallThrowsAnExceptionWhenItFailsUnexpectedly()
    {
        $pluginName = 'lrackwitz/para-alias';

        $commandline = 'composer remove lrackwitz/para-alias';

        $this->getIsInstalledJsonDecoder($pluginName);

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled();
        $process->isSuccessful()->willReturn(false);

        $this->processFactory->getProcess($commandline, $this->fileSystem->url() . '/')->shouldBeCalled();
        $this->processFactory->getProcess($commandline, $this->fileSystem->url() . '/')->willReturn($process->reveal());

        $this->pluginManager->uninstallPlugin($pluginName);
    }

    /**
     * Tests that the getInstalledPlugins() method returns an array of plugins.
     */
    public function testTheGetInstalledPluginsMethodReturnsAnArrayOfPlugins()
    {
        $plugin = $this->prophesize(PluginInterface::class);
        $plugin->getName()->willReturn('lrackwitz/para-alias', 'lrackwitz/para-sync');
        $plugin->getDescription()->willReturn('the alias description', '');
        $plugin->getVersion()->willReturn('1.1.0', '');
        $plugin->setDescription(Argument::type('string'))->shouldBeCalled();
        $plugin->setVersion(Argument::type('string'))->shouldBeCalled();

        $this->pluginFactory
            ->getPlugin(Argument::type('string'))
            ->willReturn($plugin->reveal());

        /** @var JsonEncoder $jsonEncoder */
        $jsonEncoder = $this->prophesize(JsonEncoder::class);
        $jsonEncoder->decode(Argument::any(), Argument::any())->willReturn([
            'packages' => [
                [
                    'name' => 'lrackwitz/para-alias',
                    'type' => 'para-plugin',
                    'description' => 'the alias plugin',
                    'version' => '1.1.0',
                ],
                [
                    'name' => 'composer/composer',
                ],
                [
                    'name' => 'lrackwitz/para-sync',
                    'type' => 'para-plugin',
                ],
            ],
        ]);

        $this->jsonEncoderFactory->getEncoder()->willReturn($jsonEncoder->reveal());

        $result = $this->pluginManager->getInstalledPlugins();

        $this->jsonEncoderFactory->getEncoder()->shouldHaveBeenCalled();

        $jsonEncoder->decode(Argument::type('string'), JsonEncoder::FORMAT)->shouldHaveBeenCalled();

        $this->pluginFactory->getPlugin('lrackwitz/para-alias')->shouldHaveBeenCalled();
        $this->pluginFactory->getPlugin('lrackwitz/para-sync')->shouldHaveBeenCalled();

        $this->assertEquals('lrackwitz/para-alias', $result[0]->getName(), 'Expected that the name of the first plugin has been returned correctly.');
        $this->assertEquals('the alias description', $result[0]->getDescription(), 'Expected that the correct description has been returned.');
        $this->assertEquals('1.1.0', $result[0]->getVersion(), 'Expected that the correct version has been returned.');
        $this->assertEquals('lrackwitz/para-sync', $result[1]->getName(), 'Expected that the name of the second plugin has been returned correctly.');
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy|\Symfony\Component\Serializer\Encoder\JsonEncoder
     */
    private function getIsInstalledJsonDecoder($packageName)
    {
        /** @var JsonEncoder $jsonEncoder */
        $jsonEncoder = $this->prophesize(JsonEncoder::class);
        $jsonEncoder->decode(Argument::any(), Argument::any())->willReturn(
            [
                'packages' => [
                    0 => [
                        'name' => $packageName,
                        'type' => 'para-plugin',
                    ],
                ],
            ]
        );

        $this->jsonEncoderFactory->getEncoder()->willReturn(
            $jsonEncoder->reveal()
        );

        return $jsonEncoder;
    }
}
