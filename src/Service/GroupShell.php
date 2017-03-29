<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\GroupShell.php.
 */

namespace lrackwitz\Para\Service;

use lrackwitz\Para\Event\BeforeShellCommandExecutionEvent;
use lrackwitz\Para\Event\ShellEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GroupShell.
 *
 * @package lrackwitz\Para\Service
 */
class GroupShell implements InteractiveShellInterface
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The application.
     *
     * @var Application
     */
    private $application;

    /**
     * The process factory.
     *
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * The console input.
     *
     * @var InputInterface
     */
    private $input;

    /**
     * The console output.
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * The history shell manager.
     *
     * @var HistoryShellManagerInterface
     */
    private $historyShellManager;

    /**
     * GroupShell constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Symfony\Component\Console\Application $application The application.
     * @param \lrackwitz\Para\Service\ProcessFactory $processFactory The process factory.
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher The event dispatcher.
     * @param \lrackwitz\Para\Service\HistoryShellManagerInterface $historyShellManager The history shell manager.
     * @param \Symfony\Component\Console\Input\InputInterface $input The console input.
     * @param \Symfony\Component\Console\Output\OutputInterface $output The console output.
     */
    public function __construct(
        LoggerInterface $logger,
        Application $application,
        ProcessFactory $processFactory,
        EventDispatcherInterface $dispatcher,
        HistoryShellManagerInterface $historyShellManager,
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->logger = $logger;
        $this->application = $application;
        $this->processFactory = $processFactory;
        $this->dispatcher = $dispatcher;
        $this->historyShellManager = $historyShellManager;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Starts a new shell process.
     *
     * @param $groupName
     * @param array $exludedProjects
     */
    public function run($groupName, array $exludedProjects = [])
    {
        // Set the prompt.
        $this->historyShellManager->setPrompt($this->getPrompt($groupName));

        // Disable auto exit from shell.
        $this->application->setAutoExit(false);

        // Show the welcome message.
        $this->output->writeln($this->getHeader($groupName, $exludedProjects));

        while (true) {
            // Read the command the user enters.
            $cmd = $this->readline($groupName);

            if (false === $cmd) {
                $this->output->writeln("\n");

                break;
            }

            // Add the command to the history.
            if (!empty($cmd)) {
                $this->historyShellManager->getHistory()->addCommand($cmd);
            }

            // Create an event.
            $event = new BeforeShellCommandExecutionEvent($cmd);

            // Dispatch an event to do something with the command string before running it.
            $this->dispatcher->dispatch(ShellEvents::BEFORE_SHELL_COMMAND_EXECUTION_EVENT, $event);

            if ($event->getCommand() == 'exit') {
                $this->application->setAutoExit(true);
                return;
            }

            if ($exludedProjects != []) {
                foreach ($exludedProjects as &$exludedProject) {
                    $exludedProject = '-x '.$exludedProject;
                }
            }

            $command = new StringInput(
                sprintf(
                    'execute %s "%s"'.($exludedProjects != [] ? join(
                        ' ',
                        $exludedProjects
                    ) : ''),
                    $groupName,
                    $event->getCommand()
                )
            );

            $ret = $this->application->run($command, $this->output);

            if (0 !== $ret) {
                $this->output->writeln(
                    sprintf(
                        '<error>The command terminated with an error status (%s)</error>',
                        $ret
                    )
                );
            }
        }
    }

    /**
     * Returns the shell header.
     *
     * @param string $groupName The group name.
     * @param array $excludedProjects The excluded projects.
     *
     * @return string The header string.
     */
    private function getHeader($groupName, array $excludedProjects = [])
    {
        if ($excludedProjects != []) {
            $ignoredProjects = '';
            foreach ($excludedProjects as $project) {
                $ignoredProjects .= '<comment>' . $project . '</comment>, ';
            }
            $ignoredProjects = ' except for the projects ' . substr($ignoredProjects, 0, strlen($ignoredProjects) - 2) . '.';
        } else {
            $ignoredProjects = '.';
        }

        return <<<EOF

Welcome to the <info>Para</info> shell (<comment>{$this->application->getVersion()}</comment>).

All commands you type in will be executed for each project configured in the group <comment>{$groupName}</comment>{$ignoredProjects}

To exit the shell, type <comment>exit</comment> or use the shortcut <comment>(ctrl + D)</comment>.

EOF;
    }

    /**
     * Reads a single line from standard input.
     *
     * @param string $groupName The name of the group.
     *
     * @return string The single line from standard input.
     */
    private function readline($groupName)
    {
        $this->output->write($this->getPrompt($groupName));
        $line = $this->historyShellManager->readInput();

        return $line;
    }

    /**
     * Renders a prompt.
     *
     * @param string $groupName The name of the group.
     *
     * @return string The prompt
     */
    protected function getPrompt($groupName)
    {
        // using the formatter here is required when using readline
        return $this->output->getFormatter()->format('Para <info>' . $groupName . '</info> > ');
    }
}
