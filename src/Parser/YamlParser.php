<?php

namespace Para\Parser;

use Symfony\Component\Yaml\Parser;

/**
 * Class YamlParser.
 *
 * @package Para\Parser
 */
class YamlParser implements ParserInterface
{
    /**
     * The yaml parser.
     *
     * @var Parser
     */
    private $yamlParser;

    /**
     * YamlParser constructor.
     *
     * @param Parser $parser The yaml parser.
     */
    public function __construct(Parser $parser)
    {
        $this->yamlParser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($value)
    {
        return $this->yamlParser->parse($value);
    }
}
