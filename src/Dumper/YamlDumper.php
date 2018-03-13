<?php

namespace Para\Dumper;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlDumper
 *
 * @package Para\Dumper
 */
class YamlDumper implements DumperInterface
{
    /**
     * The yaml dumper.
     *
     * @var \Symfony\Component\Yaml\Dumper
     */
    private $dumper;

    /**
     * YamlDumper constructor.
     *
     * @param \Symfony\Component\Yaml\Dumper $dumper The yaml dumper.
     */
    public function __construct(Dumper $dumper)
    {
        $this->dumper = $dumper;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(array $input): string
    {
        return $this->dumper->dump($input, 20, 0, Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE);
    }
}
