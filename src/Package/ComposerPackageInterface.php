<?php

namespace Para\Package;

/**
 * Interface ComposerPackageInterface.
 *
 * @package Para\Package
 */
interface ComposerPackageInterface
{
    /**
     * Returns the full package name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Returns the version number.
     *
     * @return string
     */
    public function getVersion(): string;
}