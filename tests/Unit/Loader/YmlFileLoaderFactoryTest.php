<?php

namespace Para\Tests\Unit\Loader;

use Para\Loader\YmlFileLoaderFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class FileLoaderFactoryTest
 *
 * @package Para\Tests\Unit\Loader
 */
class YmlFileLoaderFactoryTest extends TestCase
{
    /**
     * The file loader factory to test.
     *
     * @var \Para\Loader\FileLoaderFactoryInterface
     */
    private $fileLoaderFactory;

    /**
     * The container builder mock object.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * The file locator mock object.
     *
     * @var \Symfony\Component\Config\FileLocatorInterface
     */
    private $locator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerBuilder::class);
        $this->locator = $this->prophesize(FileLocatorInterface::class);

        $this->fileLoaderFactory = new YmlFileLoaderFactory(
            $this->container->reveal(),
            $this->locator->reveal()
        );
    }

    /**
     * Tests that the getFileLoader() method returns an instance of a file loader.
     */
    public function testTheGetFileLoaderMethodReturnsAnInstanceOfAFileLoader()
    {
        $result = $this->fileLoaderFactory->getFileLoader();

        $this->assertTrue($result instanceof LoaderInterface);
    }
}
