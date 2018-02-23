<?php

namespace Para;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer($container)
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
        $processFactory = $this->container->get('para.process_factory');
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
