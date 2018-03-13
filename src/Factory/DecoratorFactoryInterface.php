<?php

namespace Para\Factory;

use Para\Decorator\EntityArrayDecoratorInterface;
use Para\Entity\EntityInterface;

/**
 * Interface DecoratorFactoryInterface.
 *
 * @package Para\Factory
 */
interface DecoratorFactoryInterface
{
    /**
     * Returns a new array decorator.
     *
     * @param EntityInterface $entity The entity to decorate.
     *
     * @return EntityArrayDecoratorInterface The array decorator.
     */
    public function getArrayDecorator(EntityInterface $entity): EntityArrayDecoratorInterface;
}
