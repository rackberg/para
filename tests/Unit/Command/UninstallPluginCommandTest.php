<?php

namespace Para\Tests\Unit\Command;

use Para\Command\UninstallPluginCommand;
use Para\Exception\PluginNotFoundException;
use Para\Plugin\PluginManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class UninstallPluginCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class UninstallPluginCommandTest extends TestCase
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
        $this->application->add(new UninstallPluginCommand(
            $this->pluginManager->reveal()
        ));
    }

    /**
     * Tests that the execute() method returns the correct output when the plugin to uninstall is not installed.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenThePluginToUninstallIsNotInstalled()
    {
        $command = $this->application->find('plugin:uninstall');
        $parameters = [
            'command' => $command->getName(),
            'name' => 'lrackwitz/para-alias',
        ];

        $this->pluginManager
            ->uninstallPlugin(Argument::type('string'))
            ->shouldBeCalled();
        $this->pluginManager
            ->uninstallPlugin(Argument::type('string'))
            ->willThrow(new PluginNotFoundException('lrackwitz/para-alias'));

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The plugin "lrackwitz/para-alias" could not be found.', $output);
    }

    /**
     * Tests that the execute() method returns the correct output when the plugin could be uninstalled successfully.
     */
    public function testTheExecuteMethodReturnsTheCorrectOutputWhenThePluginCouldBeUninstalledSuccessfully()
    {
        $command = $this->application->find('plugin:uninstall');
        $parameters = [
            'command' => $command->getName(),
            'name' => 'lrackwitz/para-alias',
        ];

        $this->pluginManager
            ->uninstallPlugin(Argument::type('string'))
            ->shouldBeCalled();

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains('The plugin "lrackwitz/para-alias" has been uninstalled successfully.', $output);
    }
}
