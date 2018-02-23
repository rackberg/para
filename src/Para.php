<?php
/**
 * @file
 * Contains Para\Para.php.
 */

namespace Para;

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

    public function __construct()
    {
        parent::__construct('Para Console Application', $this->getRelease());
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
