<?php

namespace Para\Factory;

use Para\Service\Output\BufferedOutputAdapter;
use Para\Service\Output\BufferedOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BufferedOutputAdapterFactory
 *
 * @package Para\Factory
 */
class BufferedOutputAdapterFactory implements BufferedOutputAdapterFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOutputAdapter(OutputInterface $output): BufferedOutputInterface
    {
        return new BufferedOutputAdapter($output);
    }
}
