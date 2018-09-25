<?php

namespace Para\Plugin;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Para\Exception\PluginAlreadyInstalledException;
use Para\Exception\PluginNotFoundException;
use Para\Factory\PluginFactoryInterface;
use Para\Factory\ProcessFactoryInterface;
use Para\Package\PackageFinderInterface;
use Para\Service\Packagist\PackagistInterface;

/**
 * Class PluginManager
 *
 * @package Para\Plugin
 */
class PluginManager implements PluginManagerInterface
{
    /**
     * The composer factory.
     *
     * @var \Composer\Factory
     */
    private $composerFactory;

    /**
     * The plugin factory.
     *
     * @var \Para\Factory\PluginFactoryInterface
     */
    private $pluginFactory;

    /**
     * The process factory.
     *
     * @var \Para\Factory\ProcessFactoryInterface
     */
    private $processFactory;

    /**
     * The package finder.
     *
     * @var \Para\Package\PackageFinderInterface
     */
    private $packageFinder;

    /**
     * The packagist service.
     *
     * @var \Para\Service\Packagist\PackagistInterface
     */
    private $packagist;

    /**
     * The para root directory.
     *
     * @var string
     */
    private $rootDirectory;

    /**
     * PluginManager constructor.
     *
     * @param \Composer\Factory $composerFactory The composer factory.
     * @param \Para\Factory\PluginFactoryInterface $pluginFactory The plugin factory.
     * @param \Para\Factory\ProcessFactoryInterface $processFactory The process factory.
     * @param \Para\Package\PackageFinderInterface $packageFinder The package finder.
     * @param \Para\Service\Packagist\PackagistInterface $packagist The packagist service.
     * @param string $rootDirectory The para root directory.
     */
    public function __construct(
        Factory $composerFactory,
        PluginFactoryInterface $pluginFactory,
        ProcessFactoryInterface $processFactory,
        PackageFinderInterface $packageFinder,
        PackagistInterface $packagist,
        string $rootDirectory
    ) {
        $this->composerFactory = $composerFactory;
        $this->pluginFactory = $pluginFactory;
        $this->processFactory = $processFactory;
        $this->packageFinder = $packageFinder;
        $this->packagist = $packagist;
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchPluginsAvailable(): array
    {
        $plugins = [];

        $packages = $this->packagist->findPackagesByType('para-plugin');
        foreach ($packages as $package) {
            $plugin = $this->pluginFactory->getPlugin($package->getName());
            $plugin->setVersion($package->getVersion());
            $plugin->setDescription($package->getDescription());

            $plugins[] = $plugin;
        }

        return $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function installPlugin(string $name, string $version): string
    {
        if ($this->isInstalled($name)) {
            throw new PluginAlreadyInstalledException($name);
        }

        if ($version === 'dev') {
            $version = 'dev-master';
        }
        $process = $this->processFactory->getProcess(sprintf(
            'composer require %s %s',
            $name,
            $version
        ), $this->rootDirectory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new PluginNotFoundException($name);
        }

        $output = $process->getOutput();
        if (empty($output)) {
            $output = $process->getErrorOutput();
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstallPlugin(string $pluginName): void
    {
        if (!$this->isInstalled($pluginName)) {
            throw new PluginNotFoundException($pluginName);
        }

        $process = $this->processFactory->getProcess(sprintf(
            'composer remove %s',
            $pluginName
        ), $this->rootDirectory);

        $process->run();
        if (!$process->isSuccessful()) {
            throw new \Exception('Failed to uninstall the plugin.', 1);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isInstalled(string $pluginName): bool
    {
        $composer = $this->initComposer();
        $lockData = $this->getLockData($composer);

        foreach ($lockData['packages'] as $data) {
            if ($data['name'] === $pluginName && $data['type'] === 'para-plugin') {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstalledPlugins(): array
    {
        $composer = $this->initComposer();
        $lockData = $this->getLockData($composer);

        $plugins = [];
        foreach ($lockData['packages'] as $data) {
            if ($data['type'] === 'para-plugin') {
                $plugin = $this->pluginFactory->getPlugin($data['name']);
                $plugin->setDescription(isset($data['description']) ? $data['description'] : '');
                $plugin->setVersion(isset($data['version']) ? $data['version'] : '');

                $plugins[$data['name']] = $plugin;
            }
        }

        return $plugins;
    }

    /**
     * Initializes a new composer instance.
     *
     * @return \Composer\Composer The initalized composer instance.
     */
    private function initComposer(): Composer
    {
        $composer = $this->composerFactory->createComposer(
            new NullIO(),
            $this->rootDirectory.'composer.json',
            false,
            $this->rootDirectory,
            true
        );

        return $composer;
    }

    /**
     * Returns the composer lock data.
     *
     * @param \Composer\Composer $composer The composer.
     *
     * @return array The lock data.
     */
    private function getLockData(Composer $composer): array
    {
        return $composer->getLocker()->getLockData();
    }
}
