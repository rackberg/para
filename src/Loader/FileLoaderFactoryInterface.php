<?php

namespace Para\Loader;

use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Interface FileLoaderFactoryInterface.
 *
 * @package Para\Loader
 */
interface FileLoaderFactoryInterface
{
    /**
     * Creates a new file loader instance.
     *
     * @return \Symfony\Component\Config\Loader\LoaderInterface
     */
    public function getFileLoader(): LoaderInterface;
}
