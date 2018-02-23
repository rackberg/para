<?php

namespace Para\Tests\Unit;

use Para\Factory\ProcessFactoryInterface;
use Para\Para;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;

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
     * Tests that the method getLongVersion() returns the current git version.
     */
    public function testThatTheMethodGetLongVersionReturnsTheGitVersion()
    {
        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->willReturn(true);
        $process->getOutput()->shouldBeCalled();

        $processFactory = $this->prophesize(ProcessFactoryInterface::class);
        $processFactory->getProcess(Argument::any(), Argument::any())->willReturn($process->reveal());

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('para.process_factory')->willReturn($processFactory->reveal());

        $this->para->setContainer($container->reveal());

        $version = $this->para->getLongVersion();

        $this->assertNotEquals('unknown', $version);
    }

    /**
     * Tests that the method getLongVersion() returns the string "unknown" when the process detecting the git version fails.
     */
    public function testThatTheMethodGetLongVersionReturnsUnknownAsStringWhenTheProcessDetectingTheGitVersionFails()
    {
        $process = $this->prophesize(Process::class);
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->willReturn(false);

        $processFactory = $this->prophesize(ProcessFactoryInterface::class);
        $processFactory->getProcess(Argument::any(), Argument::any())->willReturn($process->reveal());

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('para.process_factory')->willReturn($processFactory->reveal());

        $this->para->setContainer($container->reveal());

        $version = $this->para->getLongVersion();

        $this->assertEquals('unknown', $version);
    }
}
