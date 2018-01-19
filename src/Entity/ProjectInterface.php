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
     * Returns the color code.
     *
     * @return int
     */
    public function getColorCode();

    /**
     * Sets the color code.
     *
     * @param int $colorCode
     */
    public function setColorCode(int $colorCode);

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
