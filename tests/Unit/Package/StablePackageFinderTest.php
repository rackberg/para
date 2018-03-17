<?php

namespace Para\Tests\Unit\Package;

use Composer\Package\CompletePackageInterface;
use Para\Package\StablePackageFinder;
use PHPUnit\Framework\TestCase;

/**
 * Class StablePackageFinderTest
 *
 * @package Para\Tests\Unit\Package
 */
class StablePackageFinderTest extends TestCase
{
    /**
     * The stable package finder to test.
     *
     * @var \Para\Package\PackageFinderInterface
     */
    private $packageFinder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->packageFinder = new StablePackageFinder();
    }

    /**
     * Tests that the stable package with the newest release date will be found.
     */
    public function testFindsTheStablePackageWithTheNewestReleaseDate()
    {
        $package1 = $this->prophesize(CompletePackageInterface::class);
        $package1->getReleaseDate()->shouldBeCalled();
        $package1->getReleaseDate()->willReturn(new \DateTime('+ 2 days'));

        $package2 = $this->prophesize(CompletePackageInterface::class);
        $package2->getReleaseDate()->shouldBeCalled();
        $package2->getReleaseDate()->willReturn(new \DateTime('+ 4 days'));
        $package2->getStability()->shouldBeCalled();
        $package2->getStability()->willReturn('stable');

        $packages = [$package1->reveal(), $package2->reveal()];

        $result = $this->packageFinder->findByNewestReleaseDate($packages);

        $this->assertEquals($package2->reveal(), $result);
    }
}
