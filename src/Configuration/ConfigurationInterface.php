<?php

namespace Para\Configuration;

/**
 * Interface ConfigurationInterface.
 *
 * @package Para\Configuration
 */
interface ConfigurationInterface
{
    /**
     * Loads the configuration from a file.
     *
     * @param string $fileName The file to load the configuration from.
     */
    public function load(string $fileName = null): void;

    /**
     * Saves the data into the configuration file.
     *
     * @param string $fileName The full filename.
     *
     * @return bool Returns true if the configuration has been saved successfully, otherwise false.
     */
    public function save(string $fileName = null): bool;

    /**
     * Returns the full configuration.
     *
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * Sets the path to the config file.
     *
     * @param string $configFile The path to the config file.
     */
    public function setConfigFile(string $configFile): void;
}
