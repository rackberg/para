<?php

namespace Para\Command;

use Para\Exception\PluginNotFoundException;
use Para\Plugin\PluginManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UninstallPluginCommand
 *
 * @package Para\Command
 */
class UninstallPluginCommand extends Command
{
    /**
     * The plugin manager.
     *
     * @var \Para\Plugin\PluginManagerInterface
     */
    private $pluginManager;

    /**
     * UninstallPluginCommand constructor.
     *
     * @param \Para\Plugin\PluginManagerInterface $pluginManager The plugin manager.
     */
    public function __construct(PluginManagerInterface $pluginManager)
    {
        parent::__construct();

        $this->pluginManager = $pluginManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('plugin:uninstall')
            ->setDescription('Uninstalls an installed plugin.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the plugin to uninstall'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginName = $input->getArgument('name');

        try {
            $this->pluginManager->uninstallPlugin($pluginName);

            $output->writeln(sprintf(
                '<info>The plugin "%s" has been uninstalled successfully.</info>',
                $pluginName
            ));
        } catch (PluginNotFoundException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
