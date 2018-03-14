<?php

namespace Para\Tests\Unit\Factory;

use Para\Factory\PluginFactory;
use Para\Plugin\PluginInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class PluginFactoryTest
 *
 * @package Para\Tests\Unit\Factory
 */
class PluginFactoryTest extends TestCase
{
    /**
     * Tests that the getPlugin() method returns a plugin instance.
     */
    public function testTheGetPluginMethodReturnsAPluginInstance()
    {
        $pluginFactory = new PluginFactory();

        $plugin = $pluginFactory->getPlugin('my_plugin');

        $this->assertTrue($plugin instanceof PluginInterface);
        $this->assertEquals('my_plugin', $plugin->getName());
    }
}
