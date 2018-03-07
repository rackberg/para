<?php

namespace Para\Loader;

/**
 * Interface ServicesLoaderInterface.
 *
 * @package Para\Loader
 */
interface ServicesLoaderInterface
{
    /**
     * Loads services.
     */
    public function loadServices(array $paths): void;
}
