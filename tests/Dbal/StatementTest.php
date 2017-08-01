<?php

namespace Runn\tests\Dbal\Statement;

use PDO;
use Runn\Dbal\Dbh;
use Runn\Dbal\Query;
use Runn\Dbal\Statement;

class testStatement extends Statement
{
    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        echo 'parameter:' . $parameter;
        echo 'value:' . $value;
        echo 'data_type:' . $data_type;
    }

    public function fetchColumn($column_number = 0)
    {
        echo 'arg:' . $column_number;
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = [])
    {
        echo 'style:' . $fetch_style;
        echo 'argument:' . $fetch_argument;
        echo 'ctor:' . json_encode($ctor_args);
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

    public function testBindQueryParams()
    {
        $query = (new Query())->param(':id', 1)->param(':num', 2, Dbh::PARAM_INT);
        $query->params = array_merge($query->params, ['invalid']);
        $sth = new testStatement();
        $this->expectOutputString(
            'parameter::id' . 'value:1' . 'data_type:' . Dbh::DEFAULT_PARAM_TYPE .
            'parameter::num' . 'value:2' . 'data_type:' . Dbh::PARAM_INT
        );
        $sth->bindQueryParams($query);
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
        $this->expectOutputString('style:' . \PDO::FETCH_CLASS . 'argument:Test' . 'ctor:[]');
        $sth->fetchAllObjects('Test');
    }

    public function testFetchAllObjectsWithArgs()
    {
        $sth = new testStatement();
        $this->expectOutputString('style:' . \PDO::FETCH_CLASS . 'argument:Test' . 'ctor:[1,2,3]');
        $sth->fetchAllObjects('Test', 1, 2, 3);
    }

}