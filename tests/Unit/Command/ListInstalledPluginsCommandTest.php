<?php

namespace Para\Tests\Unit\Command;

use Para\Command\ListInstalledPluginsCommand;
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
 * Class ListInstalledPluginsCommandTest
 *
 * @package Para\Tests\Unit\Command
 */
class ListInstalledPluginsCommandTest extends TestCase
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
        $this->application->add(new ListInstalledPluginsCommand(
            $this->pluginManager->reveal(),
            $this->tableOutputFactory->reveal()
        ));
    }

    /**
     * Tests that the execute() method returns a list of installed plugins.
     */
    public function testTheExecuteMethodReturnsAListOfInstalledPlugins()
    {
        $command = $this->application->find('plugins:installed');
        $parameters = [
            'command' => $command->getName()
        ];

        /** @var PluginInterface $plugin1 */
        $plugin1 = $this->prophesize(PluginInterface::class);
        $plugin1->getName()->shouldBeCalled();
        $plugin1->getName()->willReturn('plugin1');
        $plugin1->getDescription()->shouldBeCalled();
        $plugin1->getVersion()->shouldBeCalled();

        $this->pluginManager->getInstalledPlugins()->shouldBeCalled();
        $this->pluginManager->getInstalledPlugins()->willReturn([
            'plugin1' => $plugin1->reveal(),
        ]);

        $table = $this->prophesize(Table::class);
        $table->setHeaders([
            'Plugin',
            'Description',
            'Version',
        ])->shouldBeCalled();
        $table->setRows(Argument::type('array'))->shouldBeCalled();
        $table->render()->shouldBeCalled();

        $this->tableOutputFactory
            ->getTable(Argument::type(OutputInterface::class))
            ->shouldBeCalled();
        $this->tableOutputFactory
            ->getTable(Argument::type(OutputInterface::class))
            ->willReturn($table->reveal());

        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);
    }
}
