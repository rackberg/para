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
     * Reads the content of a file.
     *
     * @param string $fileName The file to read.
     */
    public function read(string $fileName): void;

    /**
     * Saves the data into the configuration file.
     *
     * @param array $configuration The configuration data to save.
     */
    public function save(array $configuration): void;
}
