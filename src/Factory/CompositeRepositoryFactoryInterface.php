<?php

namespace Para\Factory;

use Composer\Repository\CompositeRepository;

/**
 * Interface CompositeRepositoryFactoryInterface.
 *
 * @package Para\Factory
 */
interface CompositeRepositoryFactoryInterface
{
    /**
     * Returns a new instance of a CompositeRepository.
     *
     * @param array $repositories The repositories.
     *
     * @return \Composer\Repository\CompositeRepository The new instance.
     */
    public function getRepository(array $repositories): CompositeRepository;
}
