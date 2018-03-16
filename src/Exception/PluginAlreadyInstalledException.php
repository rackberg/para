<?php

namespace Para\Exception;

/**
 * Class PluginAlreadyInstalledException
 *
 * @package Para\Exception
 */
class PluginAlreadyInstalledException extends \Exception
{
    /**
     * PluginAlreadyInstalledException constructor.
     *
     * @param string $pluginName The name of the plugin.
     */
    public function __construct(string $pluginName)
    {
        parent::__construct(sprintf(
            'The plugin "%s" is already installed.',
            $pluginName
        ));
    }
}
