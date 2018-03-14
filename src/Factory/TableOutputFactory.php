<?php

namespace Para\Factory;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TableOutputFactory
 *
 * @package Para\Factory
 */
class TableOutputFactory implements TableOutputFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTable(OutputInterface $output): Table
    {
        return new Table($output);
    }
}
