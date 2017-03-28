<?php

namespace Running\tests\Dbal\Drivers\Mysql;

use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use Running\Core\Config;
use Running\Dbal\Connection;
use Running\tests\Dbal\DeployDBUnit;

abstract class DBUnit extends DeployDBUnit
{
    protected $connection;
    protected $driver;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        return $this->createDefaultDBConnection(
            (new Connection(new Config($this->settings['mysql'])))->getDbh()
        );
    }

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->settings['mysql']['driver'] = \Running\Dbal\Drivers\Mysql\Driver::class;
        $this->connection = new Connection(new Config($this->settings['mysql']));
        $this->driver = $this->connection->getDriver();
    }
}