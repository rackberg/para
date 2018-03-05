<?php

namespace Para\Configuration;

use Para\Dumper\DumperInterface;
use Para\Service\ConfigurationManagerInterface;

/**
 * Class AbstractConfiguration
 *
 * @package Para\Configuration
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * The configuration manager.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * AbstractConfiguration constructor.
     *
     * @param \Para\Service\ConfigurationManagerInterface $configurationManager The configuration manager.
     */
    protected function __construct(
        ConfigurationManagerInterface $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $fileName): void
    {
        // TODO: Implement read() method.
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $configuration): void
    {
        $content = $this->configurationManager->getDumper()->dump($configuration);
        $this->configurationManager->save($content);
    }
}
