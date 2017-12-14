<?php
/**
 * @file
 * Contains lrackwitz\Para\Tests\Service\HistoryShellManagerTest.php.
 */

namespace lrackwitz\Para\Tests\Service;

use lrackwitz\Para\Service\HistoryShellManager;
use lrackwitz\Para\Service\ShellHistoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class HistoryShellManagerTest.
 *
 * @package lrackwitz\Para\Tests\Service
 */
class HistoryShellManagerTest extends TestCase
{
    /**
     * The history shell manager to test.
     *
     * @var \lrackwitz\Para\Service\HistoryShellManagerInterface
     */
    private $shellManager;

    /**
     * The logger mock object.
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $loggerMock;

    /**
     * The shell history mock object.
     *
     * @var \lrackwitz\Para\Service\ShellHistoryInterface
     */
    private $historyMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->loggerMock = $this->prophesize(LoggerInterface::class);
        $this->historyMock = $this->prophesize(ShellHistoryInterface::class);

        $this->shellManager = new HistoryShellManager(
            $this->loggerMock->reveal(),
            $this->historyMock->reveal()
        );
    }

    /**
     * Tests that the last user input string will be returned.
     */
    public function testGetLastInputString()
    {
        $lastHistoryCommand = 'the last command';
        $this->historyMock->getLastCommand()->willReturn($lastHistoryCommand);
        $this->historyMock->getNextCommand()->willReturn('');
        $this->historyMock->getCommands()->willReturn([]);
        $this->historyMock->setCommands([])->shouldBeCalled();

        // Make sure that the end of the history has been reached.
        $this->shellManager->onDownArrowPressed();

        // Assume that the user entered a string.
        $userInput = 'the user input string';
        $this->shellManager->setUserInput($userInput);

        // The user presses the up arrow to get the last command saved in the
        // shell history.
        $this->shellManager->onUpArrowPressed();

        // Check that the user input string has changed to the last saved
        // command from the shell history.
        $this->assertEquals(
            $lastHistoryCommand,
            $this->shellManager->getUserInput(),
            'Expected that the last command from history has been returned.'
        );

        // The user presses the down arrow to get back
        // his previously entered command.
        $this->shellManager->onDownArrowPressed();

        // Check that the previously command entered by the user has been set
        // as current user input.
        $this->assertEquals(
            $userInput,
            $this->shellManager->getUserInput(),
            'Expected that the user input string has been returned.'
        );
    }
}
