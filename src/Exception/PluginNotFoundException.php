<?php

namespace Para\Exception;

/**
 * Class PluginNotFoundException
 *
 * @package Para\Exception
 */
class PluginNotFoundException extends \Exception
{
    public function __construct(string $pluginName)
    {
        parent::__construct(sprintf(
            'The plugin "%s" to install could not be found.',
            $pluginName
        ));
    }
}
