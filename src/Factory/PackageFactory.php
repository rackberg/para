<?php

namespace Para\Factory;
use Para\Package\ComposerPackage;
use Para\Package\ComposerPackageInterface;

/**
 * Implementation of a package factory interface.
 *
 * @package Para\Factory
 */
class PackageFactory implements PackageFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPackage(array $package_info = []): ComposerPackageInterface
    {
        if (empty($package_info['name'])) {
            throw new \InvalidArgumentException('The attribute "name" is missing in the package info.');
        }
        if (empty($package_info['version'])) {
            throw new \InvalidArgumentException('The attribute "version" is missing in the package info.');
        }

        $package = new ComposerPackage($package_info['name'], $package_info['version']);
        $package->setDescription(isset($package_info['description']) ? $package_info['description'] : '');

        return $package;
    }
}