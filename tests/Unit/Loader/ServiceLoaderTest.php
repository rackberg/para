<?php

namespace Para\Tests\Unit\Loader;

use Para\Loader\FileLoaderFactoryInterface;
use Para\Loader\ServicesLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * Class ServiceLoaderTest
 *
 * @package Para\Tests\Unit\Loader
 */
class ServiceLoaderTest extends TestCase
{
    /**
     * The services loader to test.
     *
     * @var \Para\Loader\ServicesLoaderInterface
     */
    private $serviceLoader;

    /**
     * The finder mock object.
     *
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * The file loader factory mock object.
     *
     * @var \Para\Loader\FileLoaderFactoryInterface
     */
    private $fileLoaderFactory;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->fileLoaderFactory = $this->prophesize(FileLoaderFactoryInterface::class);
        $this->finder = $this->prophesize(Finder::class);

        $this->serviceLoader = new ServicesLoader(
            $this->fileLoaderFactory->reveal(),
            $this->finder->reveal()
        );
    }

    public function testSomething()
    {
        $this->assertTrue(true);
    }
}
