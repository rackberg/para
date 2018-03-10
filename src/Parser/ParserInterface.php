<?php

namespace Para\Parser;

/**
 * Interface ParserInterface.
 *
 * @package Para\Parser
 */
interface ParserInterface
{
    /**
     * Parses the value.
     *
     * @param mixed $value The value to parse.
     *
     * @return mixed The parsed value.
     */
    public function parse($value);
}
