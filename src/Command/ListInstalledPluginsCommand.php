<?php

namespace Para\Command;

use Para\Factory\TableOutputFactoryInterface;
use Para\Plugin\PluginManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListInstalledPluginsCommand
 *
 * @package Para\Command
 */
class ListInstalledPluginsCommand extends Command
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
     * @var TableOutputFactoryInterface
     */
    private $tableOutputFactory;

    /**
     * ListInstalledPluginsCommand constructor.
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

    protected function configure()
    {
        $this
            ->setName('plugins:installed')
            ->setDescription('Show a list of all installed plugins.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installedPlugins = $this->pluginManager->getInstalledPlugins();

        $rows = [];
        foreach ($installedPlugins as $plugin) {
            $rows[] = [
                $plugin->getName(),
                $plugin->getDescription(),
                $plugin->getVersion(),
            ];
        }

        $table = $this->tableOutputFactory->getTable($output);
        $table->setHeaders([
            'Plugin',
            'Description',
            'Version',
        ]);
        $table->setRows($rows);
        $table->render();
    }
}
