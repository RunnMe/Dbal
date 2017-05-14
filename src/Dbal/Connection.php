<?php

namespace Runn\Dbal;

use Runn\Core\ArrayCastingInterface;
use Runn\Core\Config;

/**
 * Class Connection
 * @package Runn\Dbal
 */
class Connection
{

    /**
     * @var \Runn\Core\Config
     */
    protected $config;

    /**
     * @var \Runn\Dbal\Dbh
     */
    protected $dbh;

    /**
     * @var \Runn\Dbal\DriverInterface
     */
    protected $driver;

    /**
     * @param \Runn\Core\Config $config
     * @throws \Runn\Dbal\Exception
     * @throws \Runn\Core\Exceptions
     */
    public function __construct(Config $config)
    {
        $this->config   = $config;
        $this->dbh      = $this->getDbhByConfig($this->config);
        $this->driver   = Drivers::instance($this->config->driver);
    }

    /**
     * @param \Runn\Core\Config $config
     * @return \Runn\Dbal\Dbh
     * @throws \Runn\Dbal\Exception
     * @throws \Runn\Core\Exceptions
     */
    protected function getDbhByConfig(Config $config): Dbh
    {
        $dsn = Dsn::instance($config);

        $options = [];
        if (!empty($config->options) && $config->options instanceof ArrayCastingInterface) {
            $options = $config->options->toArrayRecursive();
        }

        try {
            $dbh = new Dbh((string)$dsn, $config->user ?? null, $config->password ?? null, $options);
            $dbh->setAttribute(Dbh::ATTR_ERRMODE, $config->errmode ?? Dbh::ERRMODE_EXCEPTION);
            $dbh->setAttribute(Dbh::ATTR_STATEMENT_CLASS, isset($config->statement) ? [$config->statement] : [Statement::class]);
            return $dbh;
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return \Runn\Core\Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return \Runn\Dbal\Dbh
     */
    public function getDbh(): Dbh
    {
        return $this->dbh;
    }

    /**
     * @return \Runn\Dbal\DriverInterface
     */
    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    /**
     * @param string|null $string
     * @param int $parameter_type
     * @return string
     */
    public function quote(string $string = null, $parameter_type = Dbh::PARAM_STR)
    {
        return $this->dbh->quote($string, $parameter_type);
    }

    /**
     * @param \Runn\Dbal\Query $query
     * @return \Runn\Dbal\Statement
     * @throws \Runn\Dbal\Exception
     */
    public function prepare(Query $query)
    {
        $sql = $this->getDriver()->getQueryBuilder()->makeQueryString($query);
        try {
            $statement = $this->dbh->prepare($sql);
            return $statement;
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param \Runn\Dbal\Query $query
     * @param array $params
     * @return bool
     */
    public function execute(Query $query, array $params = [])
    {
        $statement = $this->prepare($query);
        $params = array_merge($query->getParams(), $params);
        return $statement->execute($params);
    }

    /**
     * @param \Runn\Dbal\Query $query
     * @param array $params
     * @return \Runn\Dbal\Statement
     */
    public function query($query, array $params = [])
    {
        $statement = $this->prepare($query);
        $params = array_merge($query->getParams(), $params);
        $statement->execute($params);
        return $statement;
    }

    /**
     * @param string $name [optional] Name of the sequence object from which the ID should be returned.
     * @return string
     */
    public function lastInsertId($name = null)
    {
        return $this->dbh->lastInsertId($name);
    }

    /**
     * @return array
     */
    public function getErrorInfo()
    {
        return $this->dbh->errorInfo();
    }

    public function __sleep()
    {
        return ['config'];
    }

    public function __wakeup()
    {
        $this->dbh      = $this->getDbhByConfig($this->config);
        $this->driver   = Drivers::instance($this->config->driver);
    }

    /**
     * @return bool
     */
    public function transactionBegin()
    {
        return $this->dbh->transactionBegin();
    }

    /**
     * @return bool
     */
    public function transactionRollback()
    {
        return $this->dbh->transactionRollback();
    }

    /**
     * @return bool
     */
    public function transactionCommit()
    {
        return $this->dbh->transactionCommit();
    }

}