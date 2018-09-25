<?php

namespace Para\Package;

/**
 * Implementation of the composer package interface.
 *
 * @package Para\Package
 */
class ComposerPackage implements ComposerPackageInterface
{
    /**
     * The full package name.
     *
     * @var string
     */
    private $name;

    /**
     * The description.
     *
     * @var string
     */
    private $description;

    /**
     * The version number.
     *
     * @var string
     */
    private $version;

    /**
     * Creates a new composer package with a name and a version number.
     *
     * @param string $name The full package name.
     * @param string $version The version number.
     */
    public function __construct($name, $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * Returns the full package name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the full package name.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description.
     *
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns the version number.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Sets the version number.
     *
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

}