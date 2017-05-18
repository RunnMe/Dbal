<?php

namespace Runn\Dbal;

/**
 * Interface DriverAwareInterface
 * @package Runn\Dbal
 */
interface DriverAwareInterface
{

    /**
     * @param \Runn\Dbal\DriverInterface|null $driver
     * @return $this
     */
    public function setDriver(/*?*/DriverInterface $driver);

    /**
     * @return \Runn\Dbal\DriverInterface|null
     */
    public function getDriver(): /*?*/DriverInterface;

}