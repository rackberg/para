<?php

namespace Para\Plugin;

/**
 * Class Plugin
 *
 * @package Para\Plugin
 */
class Plugin implements PluginInterface
{
    /**
     * The plugin name.
     *
     * @var string
     */
    private $name;

    /**
     * The plugin description.
     *
     * @var string
     */
    private $description;

    /**
     * Plugin constructor.
     *
     * @param string $name The plugin name.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name of the plugin.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the description of the plugin.
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
     * @param string $description The description.
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
