<?php

namespace Para\Loader;

use Symfony\Component\Finder\Finder;

/**
 * Class ServicesLoader
 *
 * @package Para\Loader
 */
class ServicesLoader implements ServicesLoaderInterface
{
    /**
     * The finder.
     *
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * The file loader factory.
     *
     * @var \Para\Loader\FileLoaderFactoryInterface
     */
    private $factory;

    /**
     * ServicesLoader constructor.
     *
     * @param \Para\Loader\FileLoaderFactoryInterface $factory The file loader factory.
     * @param \Symfony\Component\Finder\Finder $finder The finder.
     */
    public function __construct(
        FileLoaderFactoryInterface $factory,
        Finder $finder
    ) {
        $this->factory = $factory;
        $this->finder = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function loadServices(array $paths): void
    {
        // Make sure that not existing paths are removed.
        foreach ($paths as $key => $path) {
            if (!is_dir($path)) {
                if (empty(glob($path, (defined('GLOB_BRACE') ? GLOB_BRACE : 0) | GLOB_ONLYDIR))) {
                    unset($paths[$key]);
                }
            }
        }

        try {
            $this->finder
                ->name('*.yml')
                ->in($paths);

            foreach ($this->finder as $fileInfo) {
                $loader = $this->factory->getFileLoader();

                // Load the service configurations.
                $loader->load($fileInfo->getPathname());
            }
        } catch (\Exception $e) {
        }
    }
}
