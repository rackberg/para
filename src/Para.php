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
     * Para constructor.
     */
    public function __construct()
    {
        parent::__construct('Para Console Application', $this->getRelease());
    }

    /**
     * Initializes the application.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container The dependency injection container.
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader The file loader.
     *
     * @return \Para\Para The para application instance.
     *
     * @throws \Exception When the config files could not be loaded.
     */
    public function setup(ContainerInterface $container, LoaderInterface $loader): Para
    {
        // Set the root directory.
        $container->setParameter('root_dir', __DIR__ . '/../');

        // Load the service configurations.
        $loader->load('services.yml');
        $loader->load('commands.services.yml');
        $loader->load('event.services.yml');

        $application = $container->get('para.application');
        $application->setContainer($container);

        return $application;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Returns container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function getLongVersion()
    {
        return parent::getVersion();
    }

    /**
     * Returns the current git release.
     */
    private function getRelease()
    {
        $process = new Process(
            'git describe --tags --always',
            __DIR__ . '/../'
        );
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput());
    }
}
