<?php

namespace Runn\Dbal;

/**
 * Interface DriverAwareInterface
 * @package Runn\Dbal
 *
 * @codeCoverageIgnore
 */
interface DriverAwareInterface
{

    public function setDriver(DriverInterface $driver);

    public function getDriver(): DriverInterface;

}