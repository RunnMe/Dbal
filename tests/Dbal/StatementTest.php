<?php

namespace Running\tests\Dbal\Statement;

use Running\Dbal\Statement;

class StatementTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $sth = new Statement();
        $this->assertInstanceOf(\PDOStatement::class, $sth);
        $this->assertInstanceOf(Statement::class, $sth);
    }

}