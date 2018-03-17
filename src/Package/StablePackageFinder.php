<?php

namespace Para\Package;

use Composer\Package\CompletePackageInterface;

/**
 * Class StablePackageFinder
 *
 * @package Para\Package
 */
class StablePackageFinder implements PackageFinderInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByNewestReleaseDate(array $packages): ?CompletePackageInterface
    {
        $foundPackage = null;

        foreach ($packages as $package) {
            if (!$foundPackage ||
                ($foundPackage->getReleaseDate() <= $package->getReleaseDate()
                    && $package->getStability() === 'stable')
            ) {
                $foundPackage = $package;
            }
        }

        return $foundPackage;
    }
}
