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
class Project implements ProjectInterface
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
     * {@inheritdoc}
     */
    public function getColorCode()
    {
        return $this->colorCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setColorCode(int $colorCode)
    {
        $this->colorCode = $colorCode;
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
    public function setRootDirectory(string $path)
    {
        $this->path = $path;
    }
}
