<?php
/**
 * @file
 * Contains lrackwitz\Para\Entity\Project.php.
 */

namespace lrackwitz\Para\Entity;

/**
 * Class Project.
 *
 * @package lrackwitz\Para\Entity
 */
class Project
{
    /**
     * The name of the project.
     *
     * @var string
     */
    private $name;

    /**
     * The color code.
     *
     * @var int
     */
    private $colorCode;

    /**
     * The path.
     *
     * @var string
     */
    private $path;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the color code.
     *
     * @return int
     */
    public function getColorCode()
    {
        return $this->colorCode;
    }

    /**
     * Sets the color code.
     *
     * @param int $colorCode
     */
    public function setColorCode(int $colorCode)
    {
        $this->colorCode = $colorCode;
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path.
     *
     * @param string $path
     */
    public function setRootDirectory(string $path)
    {
        $this->path = $path;
    }
}
