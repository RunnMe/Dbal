<?php

namespace Running\Dbal;

/**
 * Class Dbh
 * Data Base Handler
 * @package Running\Dbal
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