<?php

namespace Para\Tests\Unit\Command;

use Para\Command\InstallPluginCommand;
use Para\Exception\PluginAlreadyInstalledException;
use Para\Exception\PluginNotFoundException;
use Para\Plugin\PluginManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tests\Command\CommandTest;

/**
 * Class InstallPluginCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class InstallPluginCommandTest extends TestCase
{
    /**
     * The application.
     *
     * @var \Symfony\Component\Console\Application
     */
    private $application;

    /**
     * The plugin manager mock object.
     *
     * @var \Para\Plugin\PluginManagerInterface
     */
    private $pluginManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->pluginManager = $this->prophesize(PluginManagerInterface::class);
        $this->application = new Application();
        $this->application->add(new InstallPluginCommand(
            $this->pluginManager->reveal()
        ));
    }

    /**
     * Tests that the execute() method returns the correct output after plugin installation.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputAfterPluginInstallation()
    {
        $command = $this->application->find('plugin:install');
        $parameters = [
            'command' => $command->getName(),
            'name' => 'lrackwitz/alias-plugin',
            'version' => 'dev',
        ];

        $this->pluginManager
            ->installPlugin(Argument::type('string'), Argument::type('string'))
            ->shouldBeCalled();

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The plugin "lrackwitz/alias-plugin" has been installed successfully.', $output);
    }

    public function testTheExecuteMethodReturnsDebugOutputWhenVerboseModeIsActive()
    {
        $installOutput = <<<EOF
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Installing lrackwitz/plugin (dev-master 7e9bff9): Cloning 7e9bff9498 from cache
Writing lock file
Generating autoload files
EOF;

        $command = $this->application->find('plugin:install');
        $parameters = [
            'command' => $command->getName(),
            'name' => 'lrackwitz/plugin',
            'version' => 'dev'
        ];
        $options = [
            'verbosity' => OutputInterface::VERBOSITY_DEBUG,
        ];

        $this->pluginManager
            ->installPlugin(Argument::type('string'), Argument::type('string'))
            ->willReturn($installOutput);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters, $options);

        $output = $commandTester->getDisplay();

        $this->assertContains('Installing lrackwitz/plugin (dev-master', $output);
    }

    /**
     * Tests the execute() method returns the correct output when the plugin is already installed.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenThePluginIsAlreadyInstalled()
    {
        $command = $this->application->find('plugin:install');
        $parameters = [
            'command' => $command->getName(),
            'name' => 'lrackwitz/alias-plugin',
            'version' => 'dev',
        ];

        $this->pluginManager
            ->installPlugin(Argument::type('string'), Argument::type('string'))
            ->willThrow(new PluginAlreadyInstalledException('lrackwitz/para-alias'));

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The plugin "lrackwitz/para-alias" is already installed.', $output);
    }

    /**
     * Tests that the execute() method returns the correct output when the plugin to install does not exist.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenThePluginToInstallDoesNotExist()
    {
        $command = $this->application->find('plugin:install');
        $parameters = [
            'command' => $command->getName(),
            'name' => 'not-existing-plugin',
            'version' => 'dev',
        ];

        $this->pluginManager
            ->installPlugin(Argument::type('string'), Argument::type('string'))
            ->willThrow(new PluginNotFoundException('not-existing-plugin'));

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The plugin "not-existing-plugin" could not be found.', $output);
    }
}
