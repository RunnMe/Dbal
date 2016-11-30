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

    const DSN_REQUIRED = [
        'sqlite' => ['file'],
        'mysql'  => ['host', 'dbname'],
        'pgsql'  => ['host', 'dbname'],
    ];

    const DSN_OPTIONAL = [
        'sqlite' => [],
        'mysql'  => ['port', 'charset'],
        'pgsql'  => ['port'],
    ];

    /**
     * @param \Running\Core\Config $config
     * @throws \Running\Dbal\Exception
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->pdo = $this->getPdoByConfig($this->config);
    }

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
            throw $errors;
        }

        if ( !empty(static::DSN_REQUIRED[$config->driver]) ) {
            foreach (static::DSN_REQUIRED[$config->driver] as $required) {
                if (empty($config->$required)) {
                    $errors[] = new Exception('Empty ' . $required . ' in config');
                }
            }
        }
        if (!$errors->isEmpty()) {
            throw $errors;
        }

        $parts = [];

        if ( !empty(static::DSN_REQUIRED[$config->driver]) ) {
            foreach (static::DSN_REQUIRED[$config->driver] as $required) {
                if ('sqlite' == $config->driver && 'file' == $required) {
                    $parts[] = $config->$required;
                } else {
                    $parts[] = $required . '=' . $config->$required;
                }
            }
        }

        if ( !empty(static::DSN_OPTIONAL[$config->driver]) ) {
            foreach (static::DSN_OPTIONAL[$config->driver] as $required) {
                if (isset($config->$required)) {
                    $parts[] = $required . '=' . $config->$required;
                }
            }
        }

        $dsn = $config->driver . ':' . implode(';', $parts);
        return $dsn;
    }

    /**
     * @param \Running\Core\Config $config
     * @return \PDO
     * @throws \Running\Dbal\Exception
     * @throws \Running\Core\MultiException
     */
    protected function getPdoByConfig(Config $config): \PDO
    {
        $dsn = $this->getDsnByConfig($config);

        $options = [];
        if (!empty($config->options)) {
            $options = $config->options->toArrayRecursive();
        }

        try {
            $pdo = new \PDO($dsn, $config->user ?? null, $config->password ?? null, $options);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, $config->errmode ?? \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_STATEMENT_CLASS, isset($config->statement) ? [$config->statement] : [Statement::class]);
            return $pdo;
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $string
     * @param int $parameter_type
     * @return string
     */
    public function quote(string $string, $parameter_type = \PDO::PARAM_STR)
    {
        return $this->pdo->quote($string, $parameter_type);
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