<?php

namespace Para\Entity;

/**
 * Class Project.
 *
 * @package Para\Entity
 */
class Project implements ProjectInterface
{
    /**
     * The name of the project.
     *
     * @var string
     */
    private $name;

    /**
     * The foreground color.
     * @var int
     */
    private $foregroundColor;

    /**
     * The background color.
     *
     * @var int
     */
    private $backgroundColor;

    /**
     * The path.
     *
     * @var string
     */
    private $path;

    /**
     * Project constructor.
     *
     * @param string $name The name of the project.
     * @param string $path The path.
     * @param int $foregroundColor The foreground color.
     * @param int $backgroundColor The background color.
     */
    public function __construct(string $name, string $path, int $foregroundColor = null, int $backgroundColor = null)
    {
        $this->name = $name;
        $this->foregroundColor = $foregroundColor;
        $this->backgroundColor = $backgroundColor;
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns foregroundColor.
     *
     * @return int
     */
    public function getForegroundColor()
    {
        return $this->foregroundColor;
    }

    /**
     * @param int $foregroundColor
     */
    public function setForegroundColor(int $foregroundColor)
    {
        $this->foregroundColor = $foregroundColor;
    }

    /**
     * Returns backgroundColor.
     *
     * @return int
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @param int $backgroundColor
     */
    public function setBackgroundColor(int $backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }
}
