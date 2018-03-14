<?php

namespace Para\Command;

use Para\Factory\TableOutputFactoryInterface;
use Para\Plugin\PluginManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListAvailablePluginsCommand
 *
 * @package Para\Command
 */
class ListAvailablePluginsCommand extends Command
{
    /**
     * The plugin manager.
     *
     * @var \Para\Plugin\PluginManagerInterface
     */
    private $pluginManager;

    /**
     * The table output factory.
     *
     * @var \Para\Factory\TableOutputFactoryInterface
     */
    private $tableOutputFactory;

    /**
     * ListAvailablePluginsCommand constructor.
     *
     * @param \Para\Plugin\PluginManagerInterface $pluginManager The plugin manager.
     * @param \Para\Factory\TableOutputFactoryInterface $tableOutputFactory The table output factory.
     */
    public function __construct(
        PluginManagerInterface $pluginManager,
        TableOutputFactoryInterface $tableOutputFactory
    ) {
        parent::__construct();

        $this->pluginManager = $pluginManager;
        $this->tableOutputFactory = $tableOutputFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('plugins:available')
            ->setDescription('Checks for all plugins available over the web and returns a list.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $plugins = $this->pluginManager->fetchPluginsAvailable();
        if (!$plugins) {
            $output->writeln('No available plugins could be found at the moment!');
            return;
        }

        $rows = [];
        foreach ($plugins as $plugin) {
            $rows[] = [
                $plugin->getName(),
                $plugin->getDescription(),
            ];
        }

        $table = $this->tableOutputFactory->getTable($output);
        $table->setHeaders(['Plugin', 'Description', 'Enabled']);
        $table->setRows($rows);
        $table->render();
    }

}
