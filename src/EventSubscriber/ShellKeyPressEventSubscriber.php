<?php
/**
 * @file
 * Contains Para\EventSubscriber\ShellKeyPressEventSubscriber.php.
 */

namespace Para\EventSubscriber;

use Para\Event\ShellEvents;
use Para\Event\ShellKeyPressEvent;
use Para\Service\ShellManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ShellKeyPressEventSubscriber.
 *
 * @package Para\EventSubscriber
 */
class ShellKeyPressEventSubscriber implements EventSubscriberInterface
{
    /**
     * The shell manager.
     *
     * @var ShellManagerInterface
     */
    private $shellManager;

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
            ShellEvents::SHELL_KEY_PRESS_EVENT => [
                ['onKeyPressed']
            ],
        ];
    }

    public function onKeyPressed(ShellKeyPressEvent $event)
    {

    }
}
