<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\ProcessFactory.php.
 */

namespace lrackwitz\Para\Service;

use Symfony\Component\Process\Process;

/**
 * Class ProcessFactory.
 *
 * @package lrackwitz\Para\Service
 */
class ProcessFactory
{
    /**
     * Creates a new process.
     *
     *  @param string $commandline The command line to run
     *  @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *  @param array|null $env The environment variables or null to use the same environment as the current PHP process
     *  @param mixed|null $input The input as stream resource, scalar or \Traversable, or null for no input
     *  @param int|float|null $timeout The timeout in seconds or null to disable
     *  @param array $options An array of options for proc_open
     *
     *  @throws \RuntimeException When proc_open is not installed
     *
     *  @return Process
     */
    public function create(
        $commandline,
        $cwd = null,
        array $env = null,
        $input = null,
        $timeout = 60,
        array $options = array()
    ) {
        return new Process($commandline, $cwd, $env, $input, $timeout, $options);
    }
}
