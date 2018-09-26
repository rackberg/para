<?php

namespace Para\Plugin;

/**
 * Interface PluginManagerInterface.
 *
 * @package Para\Plugin
 */
interface PluginManagerInterface
{
    /**
     * Fetches all available para plugins from the web and returns them in an array.
     *
     * @return \Para\Plugin\PluginInterface[]
     */
    public function fetchPluginsAvailable(): array;

    /**
     * Installs a plugin.
     *
     * @param string $name The name of the plugin.
     * @param string $version (Optional) The version of the plugin.
     *
     * @return string The output of the process that installs the plugin.
     *
     * @throws \Para\Exception\PluginAlreadyInstalledException When the plugin is already installed.
     * @throws \Para\Exception\PluginNotFoundException When the plugin to install could not be found.
     */
    public function installPlugin(string $name, string &$version = ''): string;

    /**
     * Uninstalls a plugin.
     *
     * @param string $pluginName The name of the plugin to uninstall.
     *
     * @throws \Para\Exception\PluginNotFoundException When the plugin to uninstall is not installed.
     */
    public function uninstallPlugin(string $pluginName): void;

    /**
     * Returns if a plugin is already installed.
     *
     * @param string $pluginName The name of the plugin.
     *
     * @return bool True if the plugin is already installed, otherwise false.
     */
    public function isInstalled(string $pluginName): bool;

    /**
     * Returns the installed plugins.
     *
     * @return \Para\Plugin\PluginInterface[] The installed plugins.
     */
    public function getInstalledPlugins(): array;
}
