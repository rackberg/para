<?php

namespace Para\Factory;
use Para\Package\ComposerPackageInterface;

/**
 * Defines the methods needed to implement a package factory class.
 *
 * @package Para\Factory
 */
interface PackageFactoryInterface
{
    /**
     * Creates a new composer package instance and returns it.
     *
     * @param array $package_info The package info.
     *
     * @return ComposerPackageInterface The created package instance.
     */
    public function getPackage(array $package_info = []): ComposerPackageInterface;
}