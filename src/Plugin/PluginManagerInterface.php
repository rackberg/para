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
}
