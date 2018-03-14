<?php

namespace Para\Tests\Unit\Factory;

use Para\Factory\TableOutputFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TableOutputFactoryTest
 *
 * @package Para\Tests\Unit\Factory
 */
class TableOutputFactoryTest extends TestCase
{
    /**
     * Tests that the getTable() method returns a new table instance.
     */
    public function testTheGetTableReturnsANewTableInstance()
    {
        $output = $this->prophesize(OutputInterface::class);

        $tableOutputFactory = new TableOutputFactory();
        $table = $tableOutputFactory->getTable($output->reveal());

        $this->assertTrue($table instanceof Table);
    }
}
