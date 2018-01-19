<?php
/**
 * @file
 * Contains lrackwitz\Para\EventSubscriber\FindProjectSubscriber.php.
 */

namespace lrackwitz\Para\EventSubscriber;

use lrackwitz\Para\Event\StartSyncEvent;
use lrackwitz\Para\Service\ConfigurationManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FindProjectSubscriber.
 *
 * @package lrackwitz\Para\EventSubscriber
 */
class FindProjectSubscriber implements EventSubscriberInterface
{

    /**
     * The configuration manager.
     *
     * @var \lrackwitz\Para\Service\ConfigurationManagerInterface
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
     * @param \lrackwitz\Para\Service\ConfigurationManagerInterface $configManager
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
     * @param \lrackwitz\Para\Event\StartSyncEvent $event
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
