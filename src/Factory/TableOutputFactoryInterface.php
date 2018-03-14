<?php

namespace Para\Factory;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface TableOutputFactoryInterface.
 *
 * @package Para\Factory
 */
interface TableOutputFactoryInterface
{
    /**
     * Returns a new table helper.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output.
     *
     * @return \Symfony\Component\Console\Helper\Table The new table helper.
     */
    public function getTable(OutputInterface $output): Table;
}
