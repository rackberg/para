<?php
/**
 * @file
 * Contains lrackwitz\Para\Tests\Service\Sync\GitFileSyncerTest.php.
 */

namespace lrackwitz\Para\Tests\Service\Sync;

use lrackwitz\Para\Service\Sync\GitFileSyncer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GitFileSyncerTest.
 *
 * @package lrackwitz\Para\Tests\Service\Sync
 */
class GitFileSyncerTest extends TestCase
{
    /**
     * The git file syncer to test.
     *
     * @var \lrackwitz\Para\Service\Sync\FileSyncerInterface
     */
    private $gitFileSyncer;

    /**
     * The event dispatcher mock object.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcherMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->eventDispatcherMock = $this->prophesize(EventDispatcherInterface::class);

        $this->gitFileSyncer = new GitFileSyncer(
            $this->eventDispatcherMock->reveal()
        );
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object
     *   Instantiated object that we will run method on.
     * @param string $methodName
     *   Method name to call.
     * @param array $parameters
     *   Array of parameters to pass into method.
     *
     * @return mixed
     *   Method return.
     */
    private function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
