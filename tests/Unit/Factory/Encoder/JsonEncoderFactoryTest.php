<?php

namespace Factory\Encoder;

use Para\Factory\Encoder\JsonEncoderFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonEncoderFactoryTest.
 *
 * @package Factory\Encoder
 */
class JsonEncoderFactoryTest extends TestCase
{
    /**
     * The json encoder factory under test.
     *
     * @var \Para\Factory\Encoder\JsonEncoderFactoryInterface
     */
    private $jsonEncoderFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->jsonEncoderFactory = new JsonEncoderFactory();
    }

    /**
     * Tests the getEncoder() method.
     *
     * This test should prove that every time this method gets called a complete
     * new instance of an encoder will be returned.
     */
    public function testGetEncoderReturnsANewInstanceEveryTime()
    {
        $instance1 = $this->jsonEncoderFactory->getEncoder();
        $instance2 = $this->jsonEncoderFactory->getEncoder();

        $this->assertFalse($instance1 === $instance2, 'Expected that the instances returned are not the same.');
    }
}
