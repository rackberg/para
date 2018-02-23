<?php
/**
 * @file
 * Contains Para\Event\ShellKeyPressEvent.php.
 */

namespace Para\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ShellKeyPressEvent.
 *
 * @package Para\Event
 */
class ShellKeyPressEvent extends Event
{
    /**
     * The key.
     *
     * @var string
     */
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Returns the key.
     *
     * @return string The key.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the key.
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}
