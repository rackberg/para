<?php

namespace Factory;

use Para\Factory\PackageFactory;
use Para\Factory\PackageFactoryInterface;
use Para\Package\ComposerPackageInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test class for the package factory implementation.
 *
 * @package Factory
 */
class PackageFactoryTest extends TestCase
{
    /**
     * The package factory under test.
     *
     * @var PackageFactoryInterface
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new PackageFactory();
    }

    public function testGetPackageReturnsNewPackageInstance()
    {
        $data = [
            'name' => 'lrackwitz/para-alias',
            'description' => 'Lorem ipsum dolor sit amed',
            'version' => '1.0.0',
        ];
        $package = $this->factory->getPackage($data);

        $this->assertTrue($package instanceof ComposerPackageInterface, 'Expected a composer package interface.');
        $this->assertEquals('lrackwitz/para-alias', $package->getName(), 'Expected that the package name is not empty.');
        $this->assertEquals('Lorem ipsum dolor sit amed', $package->getDescription(), 'Expected that the package has a description.');
        $this->assertEquals('1.0.0', $package->getVersion(), 'Expected that the package has a version number.');
    }

    /**
     * Tests the getPackage() method.
     *
     * This test should prove that an exception will be thrown when the package name is missing.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetPackageThrowsExceptionWhenNameIsMissing()
    {
        $this->factory->getPackage([]);
    }

    /**
     * Tests the getPackage() method.
     *
     * This test should prove that an exception will be thrown when the package version number is missing.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetPackageThrowsExceptionWhenVersionIsMissing()
    {
        $this->factory->getPackage([
            'name' => 'some/name',
        ]);
    }
}