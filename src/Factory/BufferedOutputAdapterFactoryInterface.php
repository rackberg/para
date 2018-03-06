<?php

namespace Para\Factory;

use Para\Service\Output\BufferedOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface BufferedOutputAdapterFactoryInterface.
 *
 * @package Para\Factory
 */
interface BufferedOutputAdapterFactoryInterface
{
    /**
     * Creates a new buffered output adapter.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output.
     *
     * @return \Para\Service\Output\BufferedOutputInterface The created buffered output adapter.
     */
    public function getOutputAdapter(OutputInterface $output): BufferedOutputInterface;
}
