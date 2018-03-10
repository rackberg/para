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
     * AbstractConfiguration constructor.
     *
     * @param ParserInterface $parser The parser.
     * @param DumperInterface $dumper The dumper.
     */
    protected function __construct(
        ParserInterface $parser,
        DumperInterface $dumper
    ) {
        $this->parser = $parser;
        $this->dumper = $dumper;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $fileName): void
    {
        $content = file_get_contents($fileName);
        if (false !== $content) {
            $this->configuration = $this->parser->parse($content);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(string $fileName): bool
    {
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
}
