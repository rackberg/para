<?php

namespace Para\Tests\Unit\Factory;

use Para\Factory\ProcessFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * Class ProcessFactoryTest
 *
 * @package Para\Tests\Unit\Factory
 */
class ProcessFactoryTest extends TestCase
{
    /**
     * Tests that the getProcess() method returns a process instance.
     */
    public function testTheMethodGetProcessReturnsAnInstanceOfAProcess()
    {
        $processFactory = new ProcessFactory();
        $process = $processFactory->getProcess('pwd');

        $this->assertTrue($process instanceof Process);
    }
}
