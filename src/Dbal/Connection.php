<?php

namespace Runn\Dbal;

use Runn\Core\Config;
use Runn\Core\ConfigAwareInterface;
use Runn\Core\ConfigAwareTrait;

/**
 * Connection class - represents DB connection
 *
 * Class Connection
 * @package Runn\Dbal
 */
class Connection implements ConfigAwareInterface
{

    use ConfigAwareTrait;

    /**
     * @var Dbh|null
     */
    protected /* @7.4 ?Dbh */$dbh = null;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @param Config $config
     * @throws Exception
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config);
        if (!isset($this->getConfig()->driver)) {
            throw new Exception('Invalid config: missing driver');
        }
        $this->driver = Driver::instance($this->getConfig()->driver);
    }

    /**
     * Returns database handler
     *
     * @return Dbh
     */
    public function getDbh(): Dbh
    {
        if (null === $this->dbh) {
            $this->dbh = Dbh::instance($this->getConfig());
        }
        return $this->dbh;
    }

    /**
     * Returns database driver
     *
     * @return DriverInterface
     */
    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    /**
     * Quotes a string for use in a query
     *
     * @param string|null $string
     * @param int $parameter_type
     * @return string
     */
    public function quote(string $string = null, $parameter_type = Dbh::DEFAULT_PARAM_TYPE): string
    {
        return $this->getDbh()->quote($string, $parameter_type);
    }

    /**
     * Executes the statement directly
     *
     * @param string $sql
     * @return false|int
     */
    public function exec(string $sql)
    {
        return $this->getDbh()->exec($sql);
    }

    /**
     * Prepares a statement for execution and returns a statement object
     *
     * @param Query $query
     * @return Statement
     * @throws Exception
     */
    public function prepare(Query $query): Statement
    {
        $sql = $this->getDriver()->getQueryBuilder()->makeQueryString($query);

        try {
            $statement = $this->getDbh()->prepare($sql);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        if (false === $statement) {
            // @todo: what the statement? do we need $sql in this exception?
            throw new Exception('Database server cannot successfully prepare the statement');
        }

        return $statement;
    }

    /**
     * Executes the statement wothout the result returning
     *
     * @param \Runn\Dbal\ExecutableInterface $exec
     * @param iterable $params
     * @return bool
     * @throws Exception
     */
    public function execute(ExecutableInterface $exec, iterable $params = []): bool
    {
        if ($exec instanceof Query) {
            $exec = new Queries([$exec]);
        }

        foreach ($exec as $query) {

            $statement = $this->prepare($query);

            $statement->bindQueryParams($query);
            foreach ($params as $name => $value) {
                if (null === $value) {
                    $statement->bindValue($name, $value, Dbh::PARAM_NULL);
                } elseif (is_int($value)) {
                    $statement->bindValue($name, $value, Dbh::PARAM_INT);
                } elseif (is_bool($value)) {
                    $statement->bindValue($name, $value, Dbh::PARAM_BOOL);
                } else {
                    $statement->bindValue($name, $value);
                }
            }

            try {
                $result = $statement->execute();
            } catch (\Throwable $e) {
                throw new Exception($e->getMessage(), 0, $e);
            }
            if (false === $result) {
                // @todo: what the statement? do we need $sql in this exception?
                throw new Exception('Database server cannot successfully execute the prepared statement');
            }
        }

        return true;
    }

    /**
     * Executes the statement and returns result data
     *
     * @param Query $query
     * @param iterable $params
     * @return Statement
     * @throws Exception
     */
    public function query(Query $query, iterable $params = []): Statement
    {
        $statement = $this->prepare($query);

        $statement->bindQueryParams($query);
        foreach ($params as $name => $value) {
            if (null === $value) {
                $statement->bindValue($name, $value, Dbh::PARAM_NULL);
            } elseif (is_int($value)) {
                $statement->bindValue($name, $value, Dbh::PARAM_INT);
            } elseif (is_bool($value)) {
                $statement->bindValue($name, $value, Dbh::PARAM_BOOL);
            } else {
                $statement->bindValue($name, $value);
            }
        }

        try {
            $result = $statement->execute();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        if (false === $result) {
            // @todo: what the statement? do we need $sql in this exception?
            throw new Exception('Database server cannot successfully execute the prepared statement');
        }

        return $statement;
    }

    /**
     * @param string $name [optional] Name of the sequence object from which the ID should be returned.
     * @return string
     */
    public function lastInsertId($name = null)
    {
        return $this->getDbh()->lastInsertId($name);
    }

    /**
     * @return array
     */
    public function getErrorInfo()
    {
        return $this->getDbh()->errorInfo();
    }

    public function __sleep()
    {
        return ['config'];
    }

    public function __wakeup()
    {
        $this->dbh    = Dbh::instance($this->getConfig());
        $this->driver = Driver::instance($this->getConfig()->driver);
    }

    /**
     * @return bool
     */
    public function transactionBegin()
    {
        return $this->getDbh()->transactionBegin();
    }

    /**
     * @return bool
     */
    public function transactionRollback()
    {
        return $this->getDbh()->transactionRollback();
    }

    /**
     * @return bool
     */
    public function transactionCommit()
    {
        return $this->getDbh()->transactionCommit();
    }

}
