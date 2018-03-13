<?php

namespace Para\Decorator;

/**
 * Interface EntityArrayDecoratorInterface.
 *
 * @package Para\Decorator
 */
interface EntityArrayDecoratorInterface
{
    /**
     * Returns an array of the entity data.
     *
     * @return array The entity data.
     */
    public function asArray(): array;
}
