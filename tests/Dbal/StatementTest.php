<?php

namespace Runn\tests\Dbal\Statement;

use Runn\Dbal\Statement;

class testStatement extends Statement
{
    public function fetchColumn($column_number = 0)
    {
        echo 'arg:' . $column_number;
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = null)
    {
        echo 'style:' . $fetch_style;
        echo 'argument:' . $fetch_argument;
    }
}

class StatementTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $sth = new Statement();
        $this->assertInstanceOf(\PDOStatement::class, $sth);
        $this->assertInstanceOf(Statement::class, $sth);
    }

    public function testFetchScalar()
    {
        $sth = new testStatement();
        $this->expectOutputString('arg:0');
        $sth->fetchScalar();
    }

    public function testFetchAllObjects()
    {
        $sth = new testStatement();
        $this->expectOutputString('style:' . \PDO::FETCH_CLASS . 'argument:Test');
        $sth->fetchAllObjects('Test');
    }

}