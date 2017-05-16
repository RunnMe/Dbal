<?php

namespace Runn\Dbal;

/**
 * Custom Database Handler class extends PDO connection class
 *
 * Class Dbh
 * Data Base Handler
 * @package Runn\Dbal
 */
class Dbh
    extends \PDO
{

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