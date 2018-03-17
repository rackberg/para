<?php

namespace Para\Package;

use Composer\Package\CompletePackageInterface;

/**
 * Interface PackageFinderInterface.
 *
 * @package Para\Package
 */
interface PackageFinderInterface
{
    /**
     * Returns the package with the newest release date.
     *
     * @param CompletePackageInterface[] $packages The packages to search through.
     *
     * @return null|\Composer\Package\CompletePackageInterface The package found.
     */
    public function findByNewestReleaseDate(array $packages): ?CompletePackageInterface;
}
