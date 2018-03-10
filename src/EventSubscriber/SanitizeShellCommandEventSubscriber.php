<?php
/**
 * @file
 * Contains Para\EventSubscriber\SanitizeShellCommandEventSubscriber.php.
 */

namespace Para\EventSubscriber;

use Para\Event\BeforeShellCommandExecutionEvent;
use Para\Event\ShellEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SanitizeShellCommandEventSubscriber.
 *
 * @package Para\EventSubscriber
 */
class SanitizeShellCommandEventSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeShellCommandExecutionEvent::NAME => [
                ['beforeExecution']
            ],
        ];
    }

    /**
     * Callback method that sanitizes the command the user entered.
     *
     * @param \Para\Event\BeforeShellCommandExecutionEvent $event The event.
     */
    public function beforeExecution(BeforeShellCommandExecutionEvent $event)
    {
        // Get the command the user entered.
        $cmd = $event->getCommand();

        // Replace " with '.
        $cmd = str_replace('"', '\'', $cmd);

        $event->setCommand($cmd);
    }
}
