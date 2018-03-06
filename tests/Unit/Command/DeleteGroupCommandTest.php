<?php

namespace Para\Tests\Unit\Command;

use Para\Command\DeleteGroupCommand;
use Para\Exception\GroupNotFoundException;
use Para\Service\ConfigurationManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class DeleteGroupCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class DeleteGroupCommandTest extends TestCase
{
    /**
     * The application.
     *
     * @var \Symfony\Component\Console\Application
     */
    private $application;

    /**
     * The logger mock object.
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * The configuration manager mock object.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $configManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->configManager = $this->prophesize(ConfigurationManagerInterface::class);

        $this->application = new Application();
        $this->application->add(new DeleteGroupCommand(
            $this->logger->reveal(),
            $this->configManager->reveal()
        ));
    }

    /**
     * Tests that the execute() method returns the correct output after deleting the group.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputAfterDeletingTheGroup()
    {
        $command = $this->application->find('delete:group');
        $parameters = [
            'command' => $command->getName(),
            'group_name' => 'my_group',
        ];

        $this->configManager
            ->deleteGroup('my_group')
            ->willReturn(true);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('Successfully deleted the group from the configuration', $output);
    }

    /**
     * Tests that the execute() method returns the correct output when the group to delete is not configured.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenTheGroupToDeleteIsNotConfigured()
    {
        $command = $this->application->find('delete:group');
        $parameters = [
            'command' => $command->getName(),
            'group_name' => 'my_group',
        ];

        $this->configManager
            ->deleteGroup('my_group')
            ->willThrow(new GroupNotFoundException('my_group'));

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The group you are trying to delete is ' .
            'not stored in the configuration', $output);
    }
}
