<?php

namespace Para;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ParaApplication.
 *
 * @package Para
 */
class Para extends Application
{
    /**
     * The dependency injection container.
     *
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The dependency injection container.
     */
    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getLongVersion()
    {
        return $this->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        $version = 'unknown';

        /** @var \Para\Factory\ProcessFactoryInterface $processFactory */
        $processFactory = $this->container->get('para.factory.process_factory');
        $process = $processFactory->getProcess(
            'git describe --tags --always',
            __DIR__ . '/../'
        );

        $process->run();
        if ($process->isSuccessful()) {
            $version = trim($process->getOutput());
        }

        return $version;
    }
}
