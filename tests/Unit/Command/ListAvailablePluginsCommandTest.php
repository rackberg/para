<?php

namespace Para\Tests\Unit\Command;

use Para\Command\ListAvailablePluginsCommand;
use Para\Factory\TableOutputFactoryInterface;
use Para\Plugin\PluginInterface;
use Para\Plugin\PluginManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ListAvailablePluginsCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class ListAvailablePluginsCommandTest extends TestCase
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
     * The table output factory mock object.
     *
     * @var \Para\Factory\TableOutputFactoryInterface
     */
    private $tableOutputFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->pluginManager = $this->prophesize(PluginManagerInterface::class);
        $this->tableOutputFactory = $this->prophesize(TableOutputFactoryInterface::class);

        $this->application = new Application();
        $this->application->add(new ListAvailablePluginsCommand(
            $this->pluginManager->reveal(),
            $this->tableOutputFactory->reveal()
        ));
    }

    /**
     * Tests that the execute() method returns a table of available plugins.
     */
    public function testTheExecuteMethodReturnsATableOfAvailablePlugins()
    {
        $command = $this->application->find('plugins:available');
        $parameters = [
            'command' => $command->getName(),
        ];

        $plugin1 = $this->prophesize(PluginInterface::class);
        $plugin1->getName()->shouldBeCalled();
        $plugin1->getName()->willReturn('My awesome plugin');
        $plugin1->getDescription()->shouldBeCalled();
        $plugin1->getDescription()->willReturn('The description');
        $plugin1->getVersion()->shouldBeCalled();
        $plugin1->getVersion()->willReturn('dev-master');

        $plugin2 = $this->prophesize(PluginInterface::class);
        $plugin2->getName()->shouldBeCalled();
        $plugin2->getName()->willReturn('My awesome plugin');
        $plugin2->getDescription()->shouldBeCalled();
        $plugin2->getDescription()->willReturn('The description');
        $plugin2->getVersion()->shouldBeCalled();
        $plugin2->getVersion()->willReturn('dev-master');

        $this->pluginManager
            ->fetchPluginsAvailable()
            ->willReturn([
                'plugin1' => $plugin1->reveal(),
                'plugin2' => $plugin2->reveal(),
            ]);

        $table = $this->prophesize(Table::class);
        $table
            ->setHeaders(['Plugin', 'Description', 'Version'])
            ->shouldBeCalled();
        $table->setRows(Argument::type('array'))->shouldBeCalled();
        $table->render()->shouldBeCalled();

        $this->tableOutputFactory
            ->getTable(Argument::type(OutputInterface::class))
            ->willReturn($table->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $this->tableOutputFactory
            ->getTable(Argument::type(OutputInterface::class))
            ->shouldHaveBeenCalled();
    }

    /**
     * Tests that the execute() method returns a message when no plugins could be fetched.
     */
    public function testTheExecuteMethodReturnsAMessageWhenNoPluginsCouldBeFetched()
    {
        $command = $this->application->find('plugins:available');
        $parameters = [
            'command' => $command->getName()
        ];

        $this->pluginManager->fetchPluginsAvailable()->shouldBeCalled();
        $this->pluginManager->fetchPluginsAvailable()->willReturn([]);

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        $output = $commandTester->getDisplay();

        $this->assertContains(
            'No available plugins could be found at the moment!',
            $output
        );
    }
}
