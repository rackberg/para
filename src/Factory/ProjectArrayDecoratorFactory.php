<?php

namespace Para\Factory;

use Para\Decorator\EntityArrayDecoratorInterface;
use Para\Entity\EntityInterface;
use Para\Project\ProjectArrayDecorator;

/**
 * Class ProjectArrayDecoratorFactory.
 *
 * @package Para\Factory
 */
class ProjectArrayDecoratorFactory implements DecoratorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getArrayDecorator(EntityInterface $entity): EntityArrayDecoratorInterface
    {
        return new ProjectArrayDecorator($entity);
    }
}
