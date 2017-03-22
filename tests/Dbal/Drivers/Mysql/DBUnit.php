<?php

namespace Running\tests\Dbal\Drivers\Mysql;

use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use Running\Core\Config;
use Running\Dbal\Connection;
use Running\tests\Dbal\DeployDBUnit;

abstract class DBUnit extends DeployDBUnit
{

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        $this->settings['mysql']['driver'] = \Running\Dbal\Drivers\Mysql\Driver::class;
        return $this->createDefaultDBConnection(
            (new Connection(new Config($this->settings['mysql'])))->getDbh()
        );
    }
}