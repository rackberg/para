<?php
/**
 * @file
 * Contains lrackwitz\Para\ParaApplication.php.
 */

namespace lrackwitz\Para;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ParaApplication.
 *
 * @package Para
 */
class ParaApplication extends Application
{
    const VERSION = '1.2-alpha';

    /**
     * The dependency injection container.
     *
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        parent::__construct('Para Console Application', self::VERSION);
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


}
