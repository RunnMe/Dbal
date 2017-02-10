<?php

namespace Running\Dbal;

use Running\Core\Config;
use Running\Core\ArrayableInterface;

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
     * @var \Running\Dbal\DriverInterface
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
        if (!empty($config->options) && $config->options instanceof ArrayableInterface) {
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
     * @return \Running\Dbal\DriverInterface
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
     * @param \Running\Dbal\Query $query
     * @return \Running\Dbal\Statement
     * @throws \Running\Dbal\Exception
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
     * @param \Running\Dbal\Query $query
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