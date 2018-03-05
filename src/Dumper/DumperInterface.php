<?php

namespace Para\Dumper;

/**
 * Interface DumperInterface.
 *
 * @package Para\Dumper
 */
interface DumperInterface
{
    /**
     * Dumps an array of data into a string format.
     *
     * @param array $input The input data.
     *
     * @return string The dumped data.
     */
    public function dump(array $input): string;
}
