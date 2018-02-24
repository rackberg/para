<?php

namespace Para\Factory;

use Para\Service\InteractiveShellInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface ShellFactoryInterface
 *
 * @package Para\Factory
 */
interface ShellFactoryInterface
{
    /**
     * Creates a new shell.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The console input.
     * @param \Symfony\Component\Console\Output\OutputInterface $output The console output.
     *
     * @return \Para\Service\InteractiveShellInterface The created shell.
     */
    public function create(InputInterface $input, OutputInterface $output): InteractiveShellInterface;
}
