<?php
/**
 * @file
 * Contains lrackwitz\Para\Entity\ProjectInterface.php.
 */

namespace lrackwitz\Para\Entity;

interface ProjectInterface
{
    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the name.
     *
     * @param string $name
     */
    public function setName(string $name);

    /**
     * Returns foregroundColor.
     *
     * @return int
     */
    public function getForegroundColor();

    /**
     * @param int $foregroundColor
     */
    public function setForegroundColor(int $foregroundColor);

    /**
     * Returns backgroundColor.
     *
     * @return int
     */
    public function getBackgroundColor();

    /**
     * @param int $backgroundColor
     */
    public function setBackgroundColor(int $backgroundColor);

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Sets the path.
     *
     * @param string $path
     */
    public function setRootDirectory(string $path);
}
