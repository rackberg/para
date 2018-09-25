<?php

namespace Para\Service\Packagist;
use Para\Package\ComposerPackageInterface;

/**
 * This interface describes the methods an implementation must have to communicate with a packagist server.
 *
 * @package Para\Service\Packagist
 */
interface PackagistInterface
{
    /**
     * Returns all available versions of a package.
     *
     * @param string $package_name The full package name.
     *
     * @return array An sorted array of version numbers.
     */
    public function getPackageVersions($package_name) : array;

    /**
     * Returns all available packages with a specific type.
     *
     * @param string $type The type of the package.
     *
     * @return ComposerPackageInterface[]
     */
    public function findPackagesByType($type): array;
}