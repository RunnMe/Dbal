<?php

namespace Runn\Dbal;

abstract class DriverQueryBuilder
    implements DriverQueryBuilderInterface
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