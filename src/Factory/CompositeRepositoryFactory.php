<?php

namespace Para\Factory;

use Composer\Repository\CompositeRepository;

/**
 * Class CompositeRepositoryFactory
 *
 * @package Para\Factory
 */
class CompositeRepositoryFactory implements CompositeRepositoryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRepository(array $repositories): CompositeRepository
    {
        return new CompositeRepository($repositories);
    }
}
