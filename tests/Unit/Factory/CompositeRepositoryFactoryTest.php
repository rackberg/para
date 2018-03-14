<?php

namespace Para\Tests\Unit\Factory;

use Composer\Repository\RepositoryInterface;
use Para\Factory\CompositeRepositoryFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class CompositeRepositoryFactoryTest
 *
 * @package Para\Tests\Unit\Composer
 */
class CompositeRepositoryFactoryTest extends TestCase
{
    /**
     * Tests that the getRepository() method returns a CompositeRepository instance.
     */
    public function testTheGetRepositoryMethodReturnsACompositeRepositoryInstance()
    {
        $factory = new CompositeRepositoryFactory();
        $repository = $factory->getRepository([]);

        $this->assertTrue($repository instanceof RepositoryInterface);
    }
}
