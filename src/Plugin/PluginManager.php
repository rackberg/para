<?php

namespace Para\Plugin;

use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\CompositeRepository;
use Para\Factory\CompositeRepositoryFactoryInterface;
use Para\Factory\PluginFactoryInterface;

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
     * @param string $rootDirectory The para root directory.
     */
    public function __construct(
        CompositeRepositoryFactoryInterface $repositoryFactory,
        Factory $composerFactory,
        PluginFactoryInterface $pluginFactory,
        string $rootDirectory
    ) {
        $this->repositoryFactory = $repositoryFactory;
        $this->composerFactory = $composerFactory;
        $this->pluginFactory = $pluginFactory;
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchPluginsAvailable(): array
    {
        $plugins = [];
        $composer = $this->composerFactory->createComposer(
            new NullIO(),
            $this->rootDirectory . 'composer.json',
            false,
            $this->rootDirectory,
            true
        );
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
}
