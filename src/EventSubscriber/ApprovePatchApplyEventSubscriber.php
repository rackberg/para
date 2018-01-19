<?php
/**
 * @file
 * Contains lrackwitz\Para\EventSubscriber\ApprovePatchApplyEventSubscriber.php.
 */

namespace lrackwitz\Para\EventSubscriber;

use lrackwitz\Para\Event\ApplyPatchEvent;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ApprovePatchApplyEventSubscriber.
 *
 * @package lrackwitz\Para\EventSubscriber
 */
class ApprovePatchApplyEventSubscriber implements EventSubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ApplyPatchEvent::NAME => [
                ['beforePatchApply']
            ],
        ];
    }

    /**
     * Callback method that will be executed before a patch will be applied.
     *
     * @param \lrackwitz\Para\Event\ApplyPatchEvent $event
     *   The apply patch event.
     */
    public function beforePatchApply(ApplyPatchEvent $event)
    {
        $output = new ConsoleOutput();

        $output->writeln('<info>Changes found to apply:</info>');
        $output->writeln($event->getPatchContent());

        $question = new ConfirmationQuestion('<question>Do you want to apply the changes?</question> ', false);
        $helper = new QuestionHelper();
        if ($helper->ask(new ArgvInput(), $output, $question)) {
            $event->setApproved(true);
        } else {
            $output->writeln('<info>The user <fg=red>aborted to apply</> the changes.</info>' . "\n");
        }
    }
}
