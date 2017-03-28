<?php

namespace Running\tests\Dbal\Drivers\Mysql;

use PHPUnit_Extensions_Database_DataSet_IDataSet;
use Running\Dbal\Columns;
use Running\Dbal\Columns\StringColumn;
use Running\Dbal\Drivers\Mysql\Driver;
use Running\Dbal\Query;

class DriverTablesTest extends DBUnit
{

    public function testExistsTable()
    {
        $this->connection->execute(new Query('DROP TABLE IF EXISTS `foo`'));
        $this->assertFalse($this->driver->existsTable($this->connection, 'foo'));

        $this->connection->execute(new Query('CREATE TABLE foo (id SERIAL)'));

        $this->assertTrue($this->driver->existsTable($this->connection, 'foo'));
        $this->connection->execute(new Query('DROP TABLE `foo`'));
    }

    public function testCreateTableDDL()
    {
        $driver = new Driver();
        $method = new \ReflectionMethod(Driver::class, 'createTableDdl');
        $method->setAccessible(true);

        $columns = new Columns([
            'foo' => ['class' => StringColumn::class]
        ]);

        $this->assertSame("CREATE TABLE `test`\n(\n`foo` VARCHAR(255)\n)", $method->invoke($driver, 'test', $columns));
    }

    /**
     * @depends testExistsTable
     */
    public function testCreateTable()
    {
        $this->assertFalse($this->driver->existsTable($this->connection, 'foo'));

        $this->driver->createTable(
            $this->connection,
            'foo',
            new Columns([
                'id' => ['class' => Columns\SerialColumn::class],
                'num' => ['class' => Columns\IntColumn::class],
                'name' => ['class' => Columns\StringColumn::class]])
        );

        $this->assertTrue($this->driver->existsTable($this->connection, 'foo'));
        $queryTable = $this->getConnection()->createQueryTable('showFooColumns', 'SHOW COLUMNS FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/CreateTableTest.xml')
            ->getTable("showFooColumns");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @depends testCreateTable
     */
    public function testRenameTable()
    {
        $this->assertTrue($this->driver->existsTable($this->connection, 'foo'));
        $this->assertFalse($this->driver->existsTable($this->connection, 'bar'));

        $this->driver->renameTable($this->connection, 'foo', 'bar');

        $this->assertFalse($this->driver->existsTable($this->connection, 'foo'));
        $this->assertTrue($this->driver->existsTable($this->connection, 'bar'));
    }

    /**
     * @depends testRenameTable
     */
    public function testTruncateTable()
    {
        $this->getDatabaseTester()->setDataSet(
            $this->createXMLDataSet(__DIR__ . '/_datasets/TruncateTableTestBefore.xml')
        );
        $this->getDatabaseTester()->onSetUp();
        $actualDataSet = $this->getConnection()->createDataSet();
        $expectedDataSet = $this->createXMLDataSet(__DIR__ . '/_datasets/TruncateTableTestBefore.xml');
        $this->assertDataSetsEqual($expectedDataSet, $actualDataSet);

        $this->driver->truncateTable($this->connection, 'bar');

        $actualDataSet = $this->getConnection()->createDataSet();
        $expectedDataSet = $this->createXMLDataSet(__DIR__ . '/_datasets/TruncateTableTestAfter.xml');
        $this->assertDataSetsEqual($expectedDataSet, $actualDataSet);
    }

    /**
     * @depends testTruncateTable
     */
    public function testDropTable()
    {
        $this->assertTrue($this->driver->existsTable($this->connection, 'bar'));
        $this->driver->dropTable($this->connection, 'bar');
        $this->assertFalse($this->driver->existsTable($this->connection, 'bar'));
    }

    /**
     * @depends testDropTable
     * @expectedException \Running\Dbal\Drivers\Exception
     */
    public function testRenameTableException()
    {
        $this->driver->renameTable($this->connection, 'bar', 'foo');
    }

    /**
     * @depends testDropTable
     * @expectedException \Running\Dbal\Drivers\Exception
     */
    public function testTruncateTableException()
    {
        $this->driver->truncateTable($this->connection, 'bar');
    }

    /**
     * @depends testDropTable
     * @expectedException \Running\Dbal\Drivers\Exception
     */
    public function testDropTableException()
    {
        $this->driver->dropTable($this->connection, 'bar');
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
