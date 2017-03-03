<?php

namespace Running\Dbal;

/**
 * Interface DriverAwareInterface
 * @package Running\Dbal
 *
 * @codeCoverageIgnore
 */
interface DriverAwareInterface
{

    public function setDriver(DriverInterface $driver);

    public function getDriver(): DriverInterface;

}