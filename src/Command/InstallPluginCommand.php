<?php

namespace Para\Command;

use Para\Exception\PluginAlreadyInstalledException;
use Para\Exception\PluginNotFoundException;
use Para\Plugin\PluginManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallPluginCommand
 *
 * @package Para\Command
 */
class InstallPluginCommand extends Command
{
    /**
     * The plugin manager.
     *
     * @var \Para\Plugin\PluginManagerInterface
     */
    private $pluginManager;

    /**
     * {@inheritdoc}
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
            ->setName('plugin:install')
            ->setDescription('Installs a plugin.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the plugin.'
            )
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'The version of the plugin.',
                'dev'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginName = $input->getArgument('name');
        $version = $input->getArgument('version');

        try {
            $installOutput = $this->pluginManager->installPlugin($pluginName, $version);
            if ($output->isDebug()) {
                $output->write($installOutput);
            }

            $output->writeln(
                sprintf(
                    '<info>The plugin "%s" has been installed successfully.</info>',
                    $pluginName
                )
            );
        } catch (PluginAlreadyInstalledException $e) {
            $output->writeln('<comment>' . $e->getMessage() . '</comment>');
        } catch (PluginNotFoundException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
