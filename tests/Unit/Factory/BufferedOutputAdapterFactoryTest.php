<?php

namespace Para\Tests\Unit\Factory;

use Para\Factory\BufferedOutputAdapterFactory;
use Para\Service\Output\BufferedOutputInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BufferedOutputAdapterFactoryTest
 *
 * @package Para\Tests\Unit\Factory
 */
class BufferedOutputAdapterFactoryTest extends TestCase
{
    /**
     * The buffered output adapter factory to test.
     *
     * @var \Para\Factory\BufferedOutputAdapterFactoryInterface
     */
    private $bufferedOutputAdapterFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bufferedOutputAdapterFactory = new BufferedOutputAdapterFactory();
    }

    /**
     * Tests that the getOutputAdapter() method returns a new instance of a buffered output.
     */
    public function testTheGetOutputAdapterMethodReturnsANewInstanceOfABufferedOutput()
    {
        $output = $this->prophesize(OutputInterface::class);

        $result = $this->bufferedOutputAdapterFactory->getOutputAdapter($output->reveal());

        $this->assertTrue($result instanceof BufferedOutputInterface);
    }
}
