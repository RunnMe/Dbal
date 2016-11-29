<?php

namespace Running\Dbal;

use Running\Core\Config;
use Running\Core\MultiException;

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
     * @var \PDO
     */
    protected $pdo;

    /**
     * @param \Running\Core\Config $config
     * @throws \Running\Dbal\Exception
     */
    /*
    public function __construct(Config $config)
    {
        $this->config = $config;
        try {
            $this->pdo = $this->getPdoByConfig($this->config);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
    */

    /**
     * @param \Running\Core\Config $config
     * @return string
     * @throws \Running\Core\MultiException
     */
    protected function getDsnByConfig(Config $config): string
    {
        $errors = new MultiException();
        if (empty($config->driver)) {
            $errors[] = new Exception('Empty driver in config');
        }
        if (empty($config->host)) {
            $errors[] = new Exception('Empty host in config');
        }
        if (empty($config->dbname)) {
            $errors[] = new Exception('Empty dbname in config');
        }
        if (!$errors->isEmpty()) {
            throw $errors;
        }
        $dsn = $config->driver . ':host=' . $config->host . ';dbname=' . $config->dbname;
        if (!empty($config->port)) {
            $dsn .= ';port=' . $config->port;
        }
        return $dsn;
    }

    /**
     * @param \Running\Core\Config $config
     * @return \PDO
     * @throws \Running\Dbal\Exception
     */
    /*
    protected function getPdoByConfig(Config $config): \PDO
    {
        $dsn = $config->driver . ':host=' . $config->host . ';dbname=' . $config->dbname;
        if (!empty($config->port)) {
            $dsn .= ';port=' . $config->port;
        }
        $options = [];
        if (!empty($config->options)) {
            $options = $config->options->toArray();
        }
        try {
            $pdo = new \PDO($dsn, $config->user, $config->password, $options);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, $config->errmode ?? \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_STATEMENT_CLASS, $config->statement ? [$config->statement] : [Statement::class]);
            return $pdo;
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
    */

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
     * @param string $string
     * @param int $parameter_type
     * @return string
     */
    /*
    public function quote(string $string, $parameter_type = \PDO::PARAM_STR)
    {
        return $this->pdo->quote($string, $parameter_type);
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