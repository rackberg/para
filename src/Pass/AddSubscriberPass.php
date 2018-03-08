<?php

namespace Para\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AddSubscriberPass
 *
 * @package Para\Pass
 */
class AddSubscriberPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        // Always first check if the primary service is defined.
        if (!$container->has('event_dispatcher')) {
            return;
        }

        $definition = $container->findDefinition('event_dispatcher');

        // Find all service IDs with the kernel.event_subscriber tag.
        $subscriberServices = $container->findTaggedServiceIds('kernel.event_subscriber');

        foreach ($subscriberServices as $id => $tags) {
            // Add the subscriber service to the dispatcher service.
            $definition->addMethodCall('addSubscriber', [new Reference($id)]);
        }
    }
}
