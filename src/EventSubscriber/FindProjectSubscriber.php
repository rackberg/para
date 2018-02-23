<?php
/**
 * @file
 * Contains Para\EventSubscriber\FindProjectSubscriber.php.
 */

namespace Para\EventSubscriber;

use Para\Event\StartSyncEvent;
use Para\Service\ConfigurationManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FindProjectSubscriber.
 *
 * @package Para\EventSubscriber
 */
class FindProjectSubscriber implements EventSubscriberInterface
{

    /**
     * The configuration manager.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            StartSyncEvent::NAME => [
                ['findProject']
            ]
        ];
    }

    /**
     * FindProjectSubscriber constructor.
     *
     * @param \Para\Service\ConfigurationManagerInterface $configManager
     *   The configuration manager.
     */
    public function __construct(ConfigurationManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * Callback method.
     *
     * Will be executed when a start sync event has been triggered.
     *
     * @param \Para\Event\StartSyncEvent $event
     *   The start sync event.
     */
    public function findProject(StartSyncEvent $event)
    {
        // Find the project for the source file.
        $sourceProject = $this->configManager->findProjectByFile($event->getSourceFile());

        // Find the project for the target file.
        $targetProject = $this->configManager->findProjectByFile($event->getTargetFile());

        $event->setSourceProject($sourceProject);
        $event->setTargetProject($targetProject);
    }
}
