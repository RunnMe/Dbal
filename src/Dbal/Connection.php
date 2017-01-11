<?php

namespace Running\Dbal;

use Running\Core\Config;
use Running\Core\IArrayable;

/**
 * Class Connection
 * @package Running\Dbal
 */
class Connection
{

    /**
     * @var \Running\Core\Config
     */
    protected $config;

    /**
     * @var \Running\Dbal\Dbh
     */
    protected $dbh;

    /**
     * @var \Running\Dbal\IDriver
     */
    protected $driver;

    /**
     * @param \Running\Core\Config $config
     * @throws \Running\Dbal\Exception
     * @throws \Running\Core\MultiException
     */
    public function __construct(Config $config)
    {
        $this->config   = $config;
        $this->dbh      = $this->getDbhByConfig($this->config);
        $this->driver   = Drivers::instance($this->config->driver);
    }

    /**
     * @param \Running\Core\Config $config
     * @return \Running\Dbal\Dbh
     * @throws \Running\Dbal\Exception
     * @throws \Running\Core\MultiException
     */
    protected function getDbhByConfig(Config $config): Dbh
    {
        $dsn = Dsn::instance($config);

        $options = [];
        if (!empty($config->options) && $config->options instanceof IArrayable) {
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
     * @return \Running\Core\Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return \Running\Dbal\Dbh
     */
    public function getDbh(): Dbh
    {
        return $this->dbh;
    }

    /**
     * @return \Running\Dbal\IDriver
     */
    public function getDriver(): IDriver
    {
        return $this->driver;
    }

    /**
     * @param string $string
     * @param int $parameter_type
     * @return string
     */
    public function quote(string $string, $parameter_type = Dbh::PARAM_STR)
    {
        return $this->dbh->quote($string, $parameter_type);
    }

    /**
     * @return string
     */
    /*
    public function getDriverName()
    {
        return (string)$this->config->driver;
    }
    */

    /**
     * @return \Running\Dbal\IDriver
     */
    /*
    public function getDriver()
    {
        return DriverFactory::getDriver($this->getDriverName());
    }
    */

    /**
     * @param \Running\Dbal\Query $query
     * @return \Running\Dbal\Statement
     */
    /*
    public function prepare(Query $query)
    {
        $sql = $this->getDriver()->makeQueryString($query);
        $statement = $this->pdo->prepare($sql);
        return $statement;
    }
    */

    /**
     * @param string|\Running\Dbal\QueryBuilder|\Running\Dbal\Query $query
     * @param array $params
     * @return bool
     */
    /*
    public function execute($query, array $params = [])
    {
        if ($query instanceof QueryBuilder) {
            $params = array_merge($params, $query->getParams());
            $query = $query->getQuery($this->getDriver());
        }
        if ($query instanceof Query) {
            $params = array_merge($params, $query->params);
            $query = $this->getDriver()->makeQueryString($query);
        }
        $statement = $this->pdo->prepare($query);
        return $statement->execute($params);
    }
    */

    /**
     * @param string|\Running\Dbal\QueryBuilder|\Running\Dbal\Query $query
     * @param array $params
     * @return \Running\Dbal\Statement
     */
    /*
    public function query($query, array $params = [])
    {
        if ($query instanceof QueryBuilder) {
            $params = array_merge($params, $query->getParams());
            $query = $query->getQuery($this->getDriver());
        }
        if ($query instanceof Query) {
            $params = array_merge($params, $query->params);
            $query = $this->getDriver()->makeQueryString($query);
        }
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        return $statement;
    }
    */

    /**
     * @param string $name [optional] Name of the sequence object from which the ID should be returned.
     * @return string
     */
    /*
    public function lastInsertId($name = null)
    {
        return $this->pdo->lastInsertId($name);
    }
    */

    /**
     * @return array
     */
    /*
    public function getErrorInfo()
    {
        return $this->pdo->errorInfo();
    }
    */

    /*
    public function __sleep()
    {
        return ['config'];
    }

    public function __wakeup()
    {
        $this->pdo = $this->makePdoByConfig($this->config);
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function rollbackTransaction()
    {
        return $this->pdo->rollBack();
    }

    public function commitTransaction()
    {
        return $this->pdo->commit();
    }
    */

}