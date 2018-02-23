<?php
/**
 * @file
 * Contains Para\tests\Service\ShellHistoryTest.php.
 */

namespace Para\tests\Unit\Service;

use Para\Service\ShellHistory;
use Para\Service\ShellHistoryInterface;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Class ShellHistoryTest.
 *
 * @package Para\tests\Unit\Service
 */
class ShellHistoryTest extends TestCase
{
    /**
     * The shell history to test.
     *
     * @var ShellHistoryInterface
     */
    private $shellHistory;

    /**
     * The virtual file system.
     *
     * @var vfsStreamDirectory
     */
    private $vfsRoot;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->shellHistory = new ShellHistory();

        // Initialize the virtual file system.
        $this->vfsRoot = vfsStream::setup('para');
    }

    public function testSetCommands()
    {
        $ret = $this->shellHistory->setCommands($this->getShellCommands());

        $this->assertNull($ret, 'Asserting that the setCommands() method returns null.');

        return clone $this->shellHistory;
    }

    /**
     * @depends testSetCommands
     */
    public function testGetCommands(ShellHistoryInterface $shellHistory)
    {
        $commands = $shellHistory->getCommands();

        $this->assertEquals(
            $this->getShellCommands(),
            $commands,
            'Asserting that the test commands get returned.'
        );

        return $shellHistory;
    }

    /**
     * @depends testGetCommands
     */
    public function testClearTheHistory(ShellHistoryInterface $shellHistory)
    {
        // Prepare the history with test commands.
        $shellHistory->clear();

        $commands = $shellHistory->getCommands();

        $this->assertEquals([], $commands, 'Expected that the history is empty.');
    }

    public function testAddCommand()
    {
        $this->shellHistory->addCommand('testcommand');

        $commands = $this->shellHistory->getCommands();

        $this->assertTrue(
            in_array('testcommand', $commands),
            'Expected that the command has been added to the history.'
        );
    }

    public function testGetLastCommand()
    {
        $this->assertEquals(
            '',
            $this->shellHistory->getLastCommand(),
            'Expected that the last command is empty.'
        );

        $lastCommand = 'The last command';

        $this->shellHistory->addCommand('The first command');
        $this->shellHistory->addCommand('The second command');
        $this->shellHistory->addCommand('The third command');
        $this->shellHistory->addCommand($lastCommand);

        $command = $this->shellHistory->getLastCommand();

        $this->assertEquals(
            $lastCommand,
            $command,
            'Expected that the last command added has been returned.'
        );
    }

    public function testGetCurrentCommand()
    {
        $this->assertEquals(
            '',
            $this->shellHistory->getCurrentCommand(),
            'Expected that the current command is empty.'
        );

        $this->shellHistory->addCommand('The first command');
        $this->shellHistory->addCommand('The second command');
        $this->shellHistory->addCommand('The third command');
        $this->shellHistory->addCommand('The fourth command');

        $command = $this->shellHistory->getCurrentCommand();

        $this->assertEquals(
            'The first command',
            $command,
            'Expected that the current command is the first command.'
        );
    }

    public function testGetNextCommand()
    {
        $this->assertEquals(
            '',
            $this->shellHistory->getNextCommand(),
            'Expected that the next command is empty.'
        );

        $this->shellHistory->addCommand('The first command');
        $this->shellHistory->addCommand('The second command');
        $this->shellHistory->addCommand('The third command');
        $this->shellHistory->addCommand('The fourth command');

        $this->assertEquals(
            'The second command',
            $this->shellHistory->getNextCommand(),
            'Expected that the next command is the second command.'
        );

        $this->assertEquals(
            'The third command',
            $this->shellHistory->getNextCommand(),
            'Expected that the next command is the third command.'
        );

        $this->assertEquals(
            'The fourth command',
            $this->shellHistory->getNextCommand(),
            'Expected that the next command is the fourth command.'
        );

        $this->assertEquals(
            '',
            $this->shellHistory->getNextCommand(),
            'Expected that the next command is empty because it does not exist.'
        );
    }

    public function testGetPreviousCommand()
    {
        $this->assertEquals(
            '',
            $this->shellHistory->getPreviousCommand(),
            'Expected that the previous command is empty.'
        );

        $this->shellHistory->addCommand('The first command');
        $this->shellHistory->addCommand('The second command');
        $this->shellHistory->addCommand('The third command');
        $this->shellHistory->addCommand('The fourth command');

        // Make sure the cursor is at the last element.
        $commands = $this->shellHistory->getCommands();
        end($commands);
        $this->shellHistory->setCommands($commands);

        $this->assertEquals(
            'The third command',
            $this->shellHistory->getPreviousCommand(),
            'Expected that the previous command is the third command.'
        );

        $this->assertEquals(
            'The second command',
            $this->shellHistory->getPreviousCommand(),
            'Expected that the previous command is the second command.'
        );

        $this->assertEquals(
            'The first command',
            $this->shellHistory->getPreviousCommand(),
            'Expected that the previous command is the first command.'
        );

        $this->assertEquals(
            '',
            $this->shellHistory->getPreviousCommand(),
            'Expected that the previous command is empty because it does not exist.'
        );
    }

    /**
     * Tests that the shell history can be saved to a file.
     */
    public function testSaveHistory()
    {
        $this->saveTestHistory();

        // Check if the file has been created.
        $this->assertTrue(
            file_exists(vfsStream::url('para/.para_history')),
            'Expected that the .para_history file has been created.'
        );

        // Check if the file contains the commands.
        $this->assertEquals(
            'ls -la' . "\n" . 'pwd' . "\n" . 'echo "This is a test"' . "\n" . 'git status',
            file_get_contents(vfsStream::url('para/.para_history')),
            'Expected that the shell commands are in the file.'
        );
    }

    /**
     * Tests that a file containing shell commands can be loaded into the history.
     */
    public function testLoadHistory()
    {
        // Prepare the history file.
        $this->saveTestHistory();

        // Reset the history commands.
        $this->shellHistory->setCommands([]);

        // Read the commands from the history file.
        $this->shellHistory->loadHistory(vfsStream::url('para/.para_history'));

        // Check if the commands are loaded properly.
        $this->assertEquals(
            ['ls -la', 'pwd', 'echo "This is a test"', 'git status'],
            $this->shellHistory->getCommands(),
            'Expected that the shell commands have been loaded.'
        );
    }

    /**
     * Helper method that saves test shell commands to a file in the virtual file system.
     */
    private function saveTestHistory()
    {
        // Add commands to the history.
        $this->shellHistory->addCommand('ls -la');
        $this->shellHistory->addCommand('pwd');
        $this->shellHistory->addCommand('echo "This is a test"');
        $this->shellHistory->addCommand('git status');

        // Save the command history to a file.
        $this->shellHistory->saveHistory(vfsStream::url('para/.para_history'));
    }

    private function getShellCommands()
    {
        return [
            'pwd',
            'ls -la',
            'echo "Test"',
        ];
    }
}
