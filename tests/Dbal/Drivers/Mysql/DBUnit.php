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
            (new Connection(new Config(self::getSettings())))->getDbh()
        );
    }

    public static function getSettings()
    {
        $settings = parent::getSettings();
        if (!array_key_exists('driver', $settings['mysql'])) {
            $settings['mysql']['driver'] = \Running\Dbal\Drivers\Mysql\Driver::class;
        }
        return $settings['mysql'];
    }

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->connection = new Connection(new Config(self::getSettings()));
        $this->driver = $this->connection->getDriver();
    }
}