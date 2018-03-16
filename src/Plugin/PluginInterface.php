<?php

namespace Para\Plugin;

/**
 * Interface PluginInterface.
 *
 * @package Para\Plugin
 */
interface PluginInterface
{
    /**
     * Returns the name of the plugin.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the description of the plugin.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Sets the description.
     *
     * @param string $description The description.
     */
    public function setDescription(string $description): void;

    /**
     * Returns the version.
     *
     * @return string The version.
     */
    public function getVersion(): string;

    /**
     * Sets the version.
     *
     * @param string $version The version.
     */
    public function setVersion(string $version): void;
}
