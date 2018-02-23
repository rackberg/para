<?php

namespace Para\Tests\Unit;

use Para\Para;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ParaTest
 *
 * @package Para\Tests\Unit
 */
class ParaTest extends TestCase
{
    /**
     * The para application to test.
     *
     * @var \Para\Para
     */
    private $para;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->para = new Para();
    }

    /**
     * Tests that the setup() method initializes the dependency injection container and loads the services.
     *
     * @throws \Exception
     */
    public function testThatTheSetupMethodExecutesSuccessfully()
    {
        $application = $this->prophesize(Para::class);
        $application->setContainer(Argument::any())->shouldBeCalled();

        $container = $this->prophesize(ContainerInterface::class);
        $container
            ->get('para.application')
            ->willReturn($application->reveal());
        $container
            ->setParameter('root_dir', Argument::type('string'))
            ->shouldBeCalled();

        $loader = $this->prophesize(LoaderInterface::class);
        $loader
            ->load('services.yml')
            ->shouldBeCalled();
        $loader
            ->load('commands.services.yml')
            ->shouldBeCalled();
        $loader
            ->load('event.services.yml')
            ->shouldBeCalled();

        $result = $this->para->setup(
            $container->reveal(),
            $loader->reveal()
        );

        $this->assertTrue($result instanceof Para);
    }
}
