<?php

namespace Dbal\Drivers\Mysql;

use PHPUnit_Extensions_Database_DataSet_IDataSet;
use Running\Core\Config;
use Running\Dbal\Connection;
use Running\Dbal\Indexes\FulltextIndex;
use Running\Dbal\Indexes\SimpleIndex;
use Running\Dbal\Indexes\UniqueIndex;
use Running\Dbal\Query;
use Running\tests\Dbal\Drivers\Mysql\DBUnit;

class DriverIndexesTest extends DBUnit
{

    public function testAddIndex()
    {
        $queryTable = $this->getConnection()->createQueryTable('showFooIndexes', 'SHOW INDEXES FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/IndexesTestsWithoutIndexes.xml')
            ->getTable("showFooIndexes");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $simpleIndex = new SimpleIndex(['columns' => ['n1'], 'name' => 'simple_index']);
        $uniqueIndex = new UniqueIndex(['columns' => ['n2', 'n3'], 'name' => 'unique_index']);
        $fulltextIndex = new FulltextIndex(['columns' => ['t1'], 'name' => 'fulltext_index']);
        $this->driver->addIndex(
            $this->connection,
            'foo',
            [$simpleIndex, $uniqueIndex, $fulltextIndex]
        );

        $queryTable = $this->getConnection()->createQueryTable('showFooIndexes', 'SHOW INDEXES FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/IndexesTestsWithIndexes.xml')
            ->getTable("showFooIndexes");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @expectedException \Running\Dbal\Drivers\Exception
     */
    public function testAddIndexException()
    {
        $simpleIndex = new SimpleIndex(['columns' => ['wrongName']]);
        $this->driver->addIndex($this->connection, 'foo', [$simpleIndex]);
    }

    /**
     * @depends testAddIndex
     */
    public function testDropIndex()
    {
        $queryTable = $this->getConnection()->createQueryTable('showFooIndexes', 'SHOW INDEXES FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/IndexesTestsWithIndexes.xml')
            ->getTable("showFooIndexes");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->driver->dropIndex(
            $this->connection,
            'foo',
            ['simple_index', 'unique_index', 'fulltext_index']
        );

        $queryTable = $this->getConnection()->createQueryTable('showFooIndexes', 'SHOW INDEXES FROM `foo`');
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_datasets/IndexesTestsWithoutIndexes.xml')
            ->getTable("showFooIndexes");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @expectedException \Running\Dbal\Drivers\Exception
     */
    public function testDropIndexException()
    {
        $this->driver->dropIndex($this->connection, 'foo', ['wrongName']);
    }

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $connection = new Connection(new Config(self::getSettings()));
        $connection->execute(new Query('DROP TABLE IF EXISTS `foo`'));
        $connection->execute(new Query('CREATE TABLE `foo` (`id` SERIAL, `n1` INT, `n2` INT, `n3` INT, `t1` TEXT)'));
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
