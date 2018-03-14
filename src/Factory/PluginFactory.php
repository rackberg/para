<?php

namespace Para\Factory;

use Para\Plugin\Plugin;
use Para\Plugin\PluginInterface;

/**
 * Class PluginFactory
 *
 * @package Para\Factory
 */
class PluginFactory implements PluginFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPlugin(string $name): PluginInterface
    {
        return new Plugin($name);
    }
}
