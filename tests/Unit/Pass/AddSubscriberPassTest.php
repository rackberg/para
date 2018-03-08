<?php

namespace Para\Tests\Unit\Pass;

use Para\Pass\AddSubscriberPass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class AddSubscriberPassTest
 *
 * @package Para\Tests\Unit\Pass
 */
class AddSubscriberPassTest extends TestCase
{
    /**
     * The compiler pass to test.
     *
     * @var \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
     */
    private $addSubscriberPass;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->addSubscriberPass = new AddSubscriberPass();
    }

    /**
     * Tests that kernel.event_subscriber tagged services will be added to the event dispatcher service.
     */
    public function testSubscriberServiceWithCorrectTagWillBeAddedToTheEventDispatcher()
    {
        $definition = $this->prophesize(Definition::class);
        $definition
            ->addMethodCall('addSubscriber', Argument::any())
            ->shouldBeCalledTimes(2);

        $container = $this->prophesize(ContainerBuilder::class);
        $container
            ->has('event_dispatcher')
            ->willReturn(true);
        $container
            ->findDefinition('event_dispatcher')
            ->willReturn($definition->reveal());
        $container
            ->findTaggedServiceIds('kernel.event_subscriber')
            ->willReturn(['event_subscriber1', 'event_subscriber2']);

        $this->addSubscriberPass->process($container->reveal());
    }

    public function testAbortProcessWhenEventDispatcherServiceIsMissing()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->has('event_dispatcher')->willReturn(false);

        $result = $this->addSubscriberPass->process($container->reveal());

        $this->assertNull($result);
    }
}
