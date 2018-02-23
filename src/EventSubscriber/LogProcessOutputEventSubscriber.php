<?php
/**
 * @file
 * Contains Para\EventSubscriber\LogProcessOutputEventSubscriber.php.
 */

namespace Para\EventSubscriber;

use Para\Event\IncrementalOutputReceivedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class LogProcessOutputEventSubscriber.
 *
 * @package Para\EventSubscriber
 */
class LogProcessOutputEventSubscriber implements EventSubscriberInterface
{
    /**
     * The path where the log files are located.
     *
     * @var string
     */
    private $logPath;

    /**
     * The last command logged for a project.
     *
     * @var string[]
     */
    private $lastCommand;

    /**
     * LogProcessOutputEventSubscriber constructor.
     *
     * @param $logPath
     */
    public function __construct($logPath)
    {
        $this->logPath = $logPath;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            IncrementalOutputReceivedEvent::NAME => [
                ['writeLogMessage']
            ],
        ];
    }

    /**
     * Writes the incremental output received into a log file.
     *
     * @param \Para\Event\IncrementalOutputReceivedEvent $event
     */
    public function writeLogMessage(IncrementalOutputReceivedEvent $event)
    {
        // Get the project.
        $project = $event->getProject();

        // Get the process.
        $process = $event->getProcess();

        // Get the incremental output.
        $incrementalOutput = $event->getIncrementalOutput();

        // Get the current command.
        $command = $process->getCommandLine();

        // Create the filename.
        $filename = sprintf('%s%s.project.log', $this->logPath, strtolower($project->getName()));

        // Define the timestamp.
        $dateTime = new \DateTime();
        $timestamp = $dateTime->format('Y-m-d H:i:s');

        $fs = new Filesystem();

        if (!isset($this->lastCommand[$project->getName()]) || $this->lastCommand[$project->getName()] != $command) {
            if (file_exists($filename) && file_get_contents($filename) != '') {
                $fs->appendToFile($filename, "\n");
            }

            // Add the command executed.
            $fs->appendToFile(
                $filename,
                sprintf(
                    '[%s][Command]: %s'."\n" . '[%s][Command output]:' . "\n",
                    $timestamp,
                    $command,
                    $timestamp
                )
            );
        }

        // Add the incremental output received.
        $fs->appendToFile(
            $filename,
            sprintf('%s' . "\n", $incrementalOutput)
        );

        // Save the command executed as last command.
        $this->lastCommand[$project->getName()] = $process->getCommandLine();
    }
}
