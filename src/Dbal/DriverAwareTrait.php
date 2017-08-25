<?php

namespace Runn\Dbal;

/**
 * Trait DriverAwareTrait
 * @package Runn\Dbal
 *
 * @implements \Runn\Dbal\DriverAwareInterface
 */
trait DriverAwareTrait
{

    /** @var \Runn\Dbal\DriverInterface */
    protected $driver;

    /**
     * @param \Runn\Dbal\DriverInterface|null $driver
     * @return $this
     */
    public function setDriver(/*?*/DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return \Runn\Dbal\DriverInterface|null
     */
    public function getDriver(): /*?*/DriverInterface
    {
        return $this->driver;
    }

}