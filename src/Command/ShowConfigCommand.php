<?php
/**
 * @file
 * Contains Para\Command\ShowConfigCommand.php.
 */

namespace Para\Command;

use Para\Service\ConfigurationManagerInterface;
use Para\Service\ProcessFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShowConfigCommand.
 *
 * @package Para\Command
 */
class ShowConfigCommand extends Command
{
    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The path to the configuration files.
     *
     * @var string
     */
    private $configPath;

    /**
     * The process factory.
     *
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * ShowConfigCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     * @param \Para\Service\ProcessFactory $processFactory The process factory.
     * @param string $configPath The path where the configuration files are saved.
     */
    public function __construct(
        LoggerInterface $logger,
        ProcessFactory $processFactory,
        $configPath
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->processFactory = $processFactory;
        $this->configPath = $configPath;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('show:config')
            ->setDescription('Shows the configuration file.')
            ->setAliases(['config'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $this->configPath;

        // Check if the config file exists.
        if (!file_exists($configFile)) {
            $this->logger->error('The config file to show does not exist.', [
                'configFile' => $configFile,
            ]);
            $output->writeln('<error>The config file could not be found.</error>', 1);
            return false;
        }

        // Create a new process.
        $process = $this->processFactory->create('cat ' . $configFile);
        $process->run();
        $output->write($process->getOutput());
    }
}
