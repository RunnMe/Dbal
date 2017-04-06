<?php

namespace Dbal\Drivers\Mysql;

use PHPUnit_Extensions_Database_DataSet_IDataSet;
use Running\Core\Config;
use Running\Dbal\Connection;
use Running\Dbal\Query;
use Running\tests\Dbal\Drivers\Mysql\DBUnit;

class DriverColumnsTest extends DBUnit
{

    public function testAddColumn()
    {
        $queryTable = $this->getConnection()->createQueryTable('showFooColumns', 'SHOW COLUMNS FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/ColumnsTestsOneColumn.xml')
            ->getTable("showFooColumns");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->driver->addColumn(
            $this->connection,
            'foo',
            'bar',
            new \Running\Dbal\Columns\StringColumn()
        );

        $queryTable = $this->getConnection()->createQueryTable('showFooColumns', 'SHOW COLUMNS FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/ColumnsTestsNewColumn.xml')
            ->getTable("showFooColumns");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @depends testAddColumn
     */
    public function testDropColumn()
    {
        $queryTable = $this->getConnection()->createQueryTable('showFooColumns', 'SHOW COLUMNS FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/ColumnsTestsNewColumn.xml')
            ->getTable("showFooColumns");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->driver->dropColumn($this->connection, 'foo', 'bar');

        $queryTable = $this->getConnection()->createQueryTable('showFooColumns', 'SHOW COLUMNS FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/ColumnsTestsOneColumn.xml')
            ->getTable("showFooColumns");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @depends testDropColumn
     */
    public function testRenameColumns()
    {
        $queryTable = $this->getConnection()->createQueryTable('showFooColumns', 'SHOW COLUMNS FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/ColumnsTestsOneColumn.xml')
            ->getTable("showFooColumns");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->driver->renameColumn($this->connection, 'foo', 'id', 'foo_id');

        $queryTable = $this->getConnection()->createQueryTable('showFooColumns', 'SHOW COLUMNS FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/ColumnsTestsRenameColumn.xml')
            ->getTable("showFooColumns");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @expectedException \Running\Dbal\Drivers\Exception
     */
    public function testAddColumnException()
    {
        $this->assertFalse(
            $this->driver->
            addColumn($this->connection, 'wrongTable', 'wrongName', new \Running\Dbal\Columns\StringColumn())
        );
    }

    /**
     * @expectedException \Running\Dbal\Drivers\Exception
     */
    public function testDropColumnException()
    {
        $this->assertFalse($this->driver->dropColumn($this->connection, 'wrongTable', 'wrongName'));
    }

    /**
     * @expectedException \Running\Dbal\Drivers\Exception
     */
    public function testRenameColumnException()
    {
        $this->assertFalse($this->driver->renameColumn($this->connection, 'wrongTable', 'wrongName', 'name'));
    }

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $connection = new Connection(new Config(self::getSettings()));
        $connection->execute(new Query('DROP TABLE IF EXISTS `foo`'));
        $connection->execute(new Query('CREATE TABLE `foo` (`id` SERIAL)'));
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        $connection = new Connection(new Config(self::getSettings()));
        $connection->execute(new Query('DROP TABLE IF EXISTS `foo`'));
        parent::tearDownAfterClass();
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
    }
}
