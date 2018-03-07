<?php

namespace Para\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class YmlFileLoaderFactory
 *
 * @package Para\Loader
 */
class YmlFileLoaderFactory implements FileLoaderFactoryInterface
{
    /**
     * The container.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * The file locator.
     *
     * @var \Symfony\Component\Config\FileLocatorInterface
     */
    private $locator;

    /**
     * YmlFileLoaderFactory constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The dependency injection container.
     * @param \Symfony\Component\Config\FileLocatorInterface $locator The file locator.
     */
    public function __construct(
        ContainerBuilder $container,
        FileLocatorInterface $locator
    ) {
        $this->container = $container;
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileLoader(): LoaderInterface
    {
        return new YamlFileLoader($this->container, $this->locator);
    }
}
