<?php

namespace Para\Tests\Unit\Plugin;

use Composer\Composer;
use Composer\Factory;
use Composer\Package\Locker;
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
     * The composer factory mock object.
     *
     * @var \Composer\Factory
     */
    private $composerFactory;

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
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->composerFactory = $this->prophesize(Factory::class);
        $this->pluginFactory = $this->prophesize(PluginFactoryInterface::class);
        $this->processFactory = $this->prophesize(ProcessFactoryInterface::class);
        $this->packageFinder = $this->prophesize(PackageFinderInterface::class);
        $this->packagist = $this->prophesize(PackagistInterface::class);

        $this->pluginManager = new PluginManager(
            $this->composerFactory->reveal(),
            $this->pluginFactory->reveal(),
            $this->processFactory->reveal(),
            $this->packageFinder->reveal(),
            $this->packagist->reveal(),
            'the/path/to/the/root/directory/of/para'
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
     * Tests that a plugin installs a plugin successfully
     */
    public function testInstallsAPluginSuccessfully()
    {
        $pluginName = 'lrackwitz/para-alias';
        $pluginVersion = 'dev-master';

        $commandline = 'composer require lrackwitz/para-alias dev-master';
        $cwd = 'the/path/to/the/root/directory/of/para';

        $locker = $this->prophesize(Locker::class);
        $locker->getLockData()->shouldBeCalled();
        $locker->getLockData()->willReturn([
            'packages' => [],
        ]);

        $composer = $this->prophesize(Composer::class);
        $composer->getLocker()->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal());

        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->shouldBeCalled();
        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->willReturn($composer->reveal());

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled();
        $process->isSuccessful()->willReturn(true);
        $process->getOutput()->shouldBeCalled();
        $process->getOutput()->willReturn('something');

        $this->processFactory
            ->getProcess($commandline, $cwd)
            ->shouldBeCalled();
        $this->processFactory
            ->getProcess($commandline, $cwd)
            ->willReturn($process->reveal());

        $this->pluginManager->installPlugin($pluginName, $pluginVersion);
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
        $cwd = 'the/path/to/the/root/directory/of/para';

        $locker = $this->prophesize(Locker::class);
        $locker->getLockData()->shouldBeCalled();
        $locker->getLockData()->willReturn([
            'packages' => [],
        ]);

        $composer = $this->prophesize(Composer::class);
        $composer->getLocker()->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal());

        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->shouldBeCalled();
        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->willReturn($composer->reveal());

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled();
        $process->isSuccessful()->willReturn(false);

        $this->processFactory
            ->getProcess($commandline, $cwd)
            ->shouldBeCalled();
        $this->processFactory
            ->getProcess($commandline, $cwd)
            ->willReturn($process->reveal());

        $this->pluginManager->installPlugin($pluginName, $pluginVersion);
    }

    /**
     * Tests that the isInstalled() method returns true when the plugin is already installed.
     */
    public function testTheIsInstalledMethodReturnsTrueWhenThePluginIsAlreadyInstalled()
    {
        $pluginName = 'lrackwitz/para-alias';

        $locker = $this->prophesize(Locker::class);
        $locker->getLockData()->shouldBeCalled();
        $locker->getLockData()->willReturn([
            'packages' => [
                [
                    'name' => 'lrackwitz/para-alias',
                    'type' => 'para-plugin',
                ],
            ],
        ]);

        $composer = $this->prophesize(Composer::class);
        $composer->getLocker()->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal());

        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->shouldBeCalled();
        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->willReturn($composer->reveal());

        $result = $this->pluginManager->isInstalled($pluginName);

        $this->assertTrue($result);
    }

    /**
     * Tests that the uninstallPlugin() method throws an exception when the plugin is not installed.
     *
     * @expectedException \Para\Exception\PluginNotFoundException
     */
    public function testTheUninstallThrowsAnExceptionWhenThePluginIsNotInstalled()
    {
        $pluginName = 'lrackwitz/para-alias';

        $locker = $this->prophesize(Locker::class);
        $locker->getLockData()->shouldBeCalled();
        $locker->getLockData()->willReturn([
            'packages' => [],
        ]);

        $composer = $this->prophesize(Composer::class);
        $composer->getLocker()->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal());

        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->shouldBeCalled();
        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->willReturn($composer->reveal());

        $this->pluginManager->uninstallPlugin($pluginName);
    }

    /**
     * The that the uninstallPlugin() method is successful.
     */
    public function testThePluginUninstallIsSuccessful()
    {
        $pluginName = 'lrackwitz/para-alias';

        $commandline = 'composer remove lrackwitz/para-alias';
        $cwd = 'the/path/to/the/root/directory/of/para';

        $locker = $this->prophesize(Locker::class);
        $locker->getLockData()->shouldBeCalled();
        $locker->getLockData()->willReturn([
            'packages' => [
                [
                    'name' => 'lrackwitz/para-alias',
                    'type' => 'para-plugin',
                ],
            ],
        ]);

        $composer = $this->prophesize(Composer::class);
        $composer->getLocker()->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal());

        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->shouldBeCalled();
        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->willReturn($composer->reveal());

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled();
        $process->isSuccessful()->willReturn(true);

        $this->processFactory->getProcess($commandline, $cwd)->shouldBeCalled();
        $this->processFactory->getProcess($commandline, $cwd)->willReturn($process->reveal());

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
        $cwd = 'the/path/to/the/root/directory/of/para';

        $locker = $this->prophesize(Locker::class);
        $locker->getLockData()->shouldBeCalled();
        $locker->getLockData()->willReturn([
            'packages' => [
                [
                    'name' => 'lrackwitz/para-alias',
                    'type' => 'para-plugin',
                ],
            ],
        ]);

        $composer = $this->prophesize(Composer::class);
        $composer->getLocker()->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal());

        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->shouldBeCalled();
        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->willReturn($composer->reveal());

        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled();
        $process->isSuccessful()->willReturn(false);

        $this->processFactory->getProcess($commandline, $cwd)->shouldBeCalled();
        $this->processFactory->getProcess($commandline, $cwd)->willReturn($process->reveal());

        $this->pluginManager->uninstallPlugin($pluginName);
    }

    /**
     * Tests that the getInstalledPlugins() method returns an array of plugins.
     */
    public function testTheGetInstalledPluginsMethodReturnsAnArrayOfPlugins()
    {
        $locker = $this->prophesize(Locker::class);
        $locker->getLockData()->shouldBeCalled();
        $locker->getLockData()->willReturn([
            'packages' => [
                [
                    'name' => 'lrackwitz/para-alias',
                    'type' => 'para-plugin',
                ],
            ],
        ]);

        $composer = $this->prophesize(Composer::class);
        $composer->getLocker()->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal());

        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->shouldBeCalled();
        $this->composerFactory
            ->createComposer(Argument::any(), Argument::type('string'), false, Argument::type('string'), true)
            ->willReturn($composer->reveal());

        $plugin = $this->prophesize(PluginInterface::class);
        $plugin->setDescription(Argument::type('string'))->shouldBeCalled();
        $plugin->setVersion(Argument::type('string'))->shouldBeCalled();

        $this->pluginFactory
            ->getPlugin('lrackwitz/para-alias')
            ->shouldBeCalled();
        $this->pluginFactory
            ->getPlugin('lrackwitz/para-alias')
            ->willReturn($plugin->reveal());

        $result = $this->pluginManager->getInstalledPlugins();

        $this->assertTrue(is_array($result));
    }
}
