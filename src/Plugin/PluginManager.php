<?php

namespace Para\Plugin;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\CompositeRepository;
use Para\Exception\PluginAlreadyInstalledException;
use Para\Exception\PluginNotFoundException;
use Para\Factory\CompositeRepositoryFactoryInterface;
use Para\Factory\PluginFactoryInterface;
use Para\Factory\ProcessFactoryInterface;

/**
 * Class PluginManager
 *
 * @package Para\Plugin
 */
class PluginManager implements PluginManagerInterface
{
    /**
     * The repository factory.
     *
     * @var \Para\Factory\CompositeRepositoryFactoryInterface
     */
    private $repositoryFactory;

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
     * The para root directory.
     *
     * @var string
     */
    private $rootDirectory;

    /**
     * PluginManager constructor.
     *
     * @param \Para\Factory\CompositeRepositoryFactoryInterface $repositoryFactory The repository factory.
     * @param \Composer\Factory $composerFactory The composer factory.
     * @param \Para\Factory\PluginFactoryInterface $pluginFactory The plugin factory.
     * @param \Para\Factory\ProcessFactoryInterface $processFactory The process factory.
     * @param string $rootDirectory The para root directory.
     */
    public function __construct(
        CompositeRepositoryFactoryInterface $repositoryFactory,
        Factory $composerFactory,
        PluginFactoryInterface $pluginFactory,
        ProcessFactoryInterface $processFactory,
        string $rootDirectory
    ) {
        $this->repositoryFactory = $repositoryFactory;
        $this->composerFactory = $composerFactory;
        $this->pluginFactory = $pluginFactory;
        $this->processFactory = $processFactory;
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchPluginsAvailable(): array
    {
        $plugins = [];
        $composer = $this->initComposer();
        $repositories = $composer->getRepositoryManager()->getRepositories();
        $compositeRepository = $this->repositoryFactory->getRepository($repositories);
        $packages = $compositeRepository->search('', CompositeRepository::SEARCH_FULLTEXT, 'para-plugin');
        foreach ($packages as $package) {
            $plugin = $this->pluginFactory->getPlugin($package['name']);
            $plugin->setDescription($package['description'] ?: '');

            $plugins[$package['name']] = $plugin;
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
