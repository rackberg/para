<?php
/**
 * @file
 * Contains lrackwitz\Para\EventSubscriber\AddEnvironmentVariableEventSubscriber.php.
 */

namespace lrackwitz\Para\EventSubscriber;

use lrackwitz\Para\Event\PostProcessCreationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AddEnvironmentVariableEventSubscriber.
 *
 * @package lrackwitz\Para\EventSubscriber
 */
class AddEnvironmentVariableEventSubscriber implements EventSubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PostProcessCreationEvent::NAME => [
                ['addEnvironmentVariable']
            ],
        ];
    }

    /**
     * This callback method adds an environment variable to a created process.
     *
     * @param \lrackwitz\Para\Event\PostProcessCreationEvent $event
     */
    public function addEnvironmentVariable(PostProcessCreationEvent $event)
    {
        // Get the current set environment variables.
        $env = $event->getProcess()->getEnv();

        // Add the "para_project" environment variable
        $env['para_project'] = $event->getProject()->getName();

        // Save the environment variables in the process.
        $event->getProcess()->setEnv($env);
    }
}
