<?php

namespace Runn\Dbal;

use Runn\Core\ArrayCastingInterface;
use Runn\Core\Config;
use Runn\Core\InstanceableByConfigInterface;

/**
 * Custom Database Handler class extends PDO connection class
 *
 * Class Dbh
 * Data Base Handler
 * @package Runn\Dbal
 */
class Dbh
    extends \PDO
    implements InstanceableByConfigInterface
{

    public const DEFAULT_PARAM_TYPE = self::PARAM_STR;

    /**
     * @param \Runn\Core\Config|null $config
     * @return static
     * @throws \Runn\Dbal\Exception
     */
    public static function instance(Config $config = null)
    {
        $dsn = Dsn::instance($config);

        $options = [];
        if (!empty($config->options) && $config->options instanceof ArrayCastingInterface) {
            $options = $config->options->toArrayRecursive();
        }

        try {
            $dbh = new static((string)$dsn, $config->user ?? null, $config->password ?? null, $options);
            $dbh->setAttribute(self::ATTR_ERRMODE, $config->errmode ?? self::ERRMODE_EXCEPTION);
            $dbh->setAttribute(self::ATTR_STATEMENT_CLASS, isset($config->statement) ? [$config->statement] : [Statement::class]);
            return $dbh;
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws \BadMethodCallException
     */
    public function beginTransaction()
    {
        throw new \BadMethodCallException();
    }

    /**
     * @throws \BadMethodCallException
     */
    public function commit()
    {
        throw new \BadMethodCallException();
    }

    /**
     * @throws \BadMethodCallException
     */
    public function rollBack()
    {
        throw new \BadMethodCallException();
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    public function transactionBegin()
    {
        return parent::beginTransaction();
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    public function transactionCommit()
    {
        return parent::commit();
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    public function transactionRollback()
    {
        return parent::rollBack();
    }

}