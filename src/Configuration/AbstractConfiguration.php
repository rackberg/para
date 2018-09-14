<?php

namespace Para\Configuration;

use Para\Dumper\DumperInterface;
use Para\Parser\ParserInterface;

/**
 * Class AbstractConfiguration
 *
 * @package Para\Configuration
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * The full configuration.
     *
     * @var string[]
     */
    protected $configuration = [];

    /**
     * The parser.
     *
     * @var ParserInterface
     */
    protected $parser;

    /**
     * The dumper.
     *
     * @var DumperInterface
     */
    protected $dumper;

    /**
     * The path to the config file.
     *
     * @var string
     */
    protected $configFile;

    /**
     * AbstractConfiguration constructor.
     *
     * @param ParserInterface $parser The parser.
     * @param DumperInterface $dumper The dumper.
     * @param string $configFile The path to the config file.
     */
    protected function __construct(
        ParserInterface $parser,
        DumperInterface $dumper,
        string $configFile
    ) {
        $this->parser = $parser;
        $this->dumper = $dumper;
        $this->configFile = $configFile;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $fileName = null): void
    {
        if (!$fileName) {
            $fileName = $this->configFile;
        }
        if (file_exists($fileName)) {
            $content = file_get_contents($fileName);
            if (false !== $content) {
                $this->configuration = $this->parser->parse($content);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(string $fileName = null): bool
    {
        if (!$fileName) {
            $fileName = $this->configFile;
        }
        $content = $this->dumper->dump($this->configuration);
        return file_put_contents($fileName, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigFile(string $configFile): void
    {
        $this->configFile = $configFile;
    }
}
