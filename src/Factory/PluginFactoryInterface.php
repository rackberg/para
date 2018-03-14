<?php

namespace Para\Factory;

use Para\Plugin\PluginInterface;

/**
 * Interface PluginFactoryInterface.
 *
 * @package Para\Factory
 */
interface PluginFactoryInterface
{
    /**
     * Returns a new instance of a plugin.
     *
     * @param string $name The name of the plugin.
     *
     * @return \Para\Plugin\PluginInterface The new plugin instance.
     */
    public function getPlugin(string $name): PluginInterface;
}
