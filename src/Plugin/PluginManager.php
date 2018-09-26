<?php

namespace Para\Plugin;

use Para\Exception\PluginAlreadyInstalledException;
use Para\Exception\PluginNotFoundException;
use Para\Factory\Encoder\JsonEncoderFactoryInterface;
use Para\Factory\PluginFactoryInterface;
use Para\Factory\ProcessFactoryInterface;
use Para\Package\PackageFinderInterface;
use Para\Service\Packagist\PackagistInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class PluginManager
 *
 * @package Para\Plugin
 */
class PluginManager implements PluginManagerInterface
{
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
     * The serializer factory.
     *
     * @var JsonEncoderFactoryInterface
     */
    private $jsonEncoderFactory;

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
     * @param \Para\Factory\PluginFactoryInterface $pluginFactory The plugin factory.
     * @param \Para\Factory\ProcessFactoryInterface $processFactory The process factory.
     * @param \Para\Factory\Encoder\JsonEncoderFactoryInterface $jsonEncoderFactory The json encoder factory.
     * @param \Para\Package\PackageFinderInterface $packageFinder The package finder.
     * @param \Para\Service\Packagist\PackagistInterface $packagist The packagist service.
     * @param string $rootDirectory The para root directory.
     */
    public function __construct(
        PluginFactoryInterface $pluginFactory,
        ProcessFactoryInterface $processFactory,
        JsonEncoderFactoryInterface $jsonEncoderFactory,
        PackageFinderInterface $packageFinder,
        PackagistInterface $packagist,
        string $rootDirectory
    ) {
        $this->pluginFactory = $pluginFactory;
        $this->processFactory = $processFactory;
        $this->jsonEncoderFactory = $jsonEncoderFactory;
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
    public function installPlugin(string $name, string &$version = ''): string
    {
        if ($this->isInstalled($name)) {
            throw new PluginAlreadyInstalledException($name);
        }

        if (empty($version)) {
            $versions = $this->packagist->getPackageVersions($name);
            $version = $this->packagist->getHighestVersion($versions);
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
        $data = $this->decodeLockFile();

        if (!empty($data['packages'])) {
            foreach ($data['packages'] as $package) {
                if ($this->isParaPlugin($package) && $package['name'] === $pluginName) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstalledPlugins(): array
    {
        $plugins = [];

        $data = $this->decodeLockFile();

        if (!empty($data['packages'])) {
            foreach ($data['packages'] as $package) {
                if ($this->isParaPlugin($package)) {
                    $plugin = $this->pluginFactory->getPlugin($package['name']);
                    $plugin->setDescription(isset($package['description']) ? $package['description'] : '');
                    $plugin->setVersion(isset($package['version']) ? $package['version'] : '');
                    $plugins[] = $plugin;
                }
            }
        }

        return $plugins;
    }

    /**
     * Returns the decoded lock file data.
     *
     * @return array
     */
    private function decodeLockFile(): array
    {
        $data = [];

        $encoder = $this->jsonEncoderFactory->getEncoder();
        if (file_exists($this->rootDirectory . 'composer.lock')) {
            $data = $encoder->decode(
                file_get_contents($this->rootDirectory.'composer.lock'),
                JsonEncoder::FORMAT
            );
        }

        return $data;
    }

    /**
     * Returns true when the package is of type para-plugin.
     *
     * @param \stdClass $package The package stdClass object.
     *
     * @return bool
     */
    private function isParaPlugin($package): bool
    {
        if (isset($package['type']) && $package['type'] === 'para-plugin') {
            return true;
        }

        return false;
    }
}
