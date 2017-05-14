<?php

namespace Runn\Dbal;

/**
 * Interface DriverQueryBuilderInterface
 * @package Runn\Dbal
 *
 * @codeCoverageIgnore
 */
interface DriverQueryBuilderInterface
{

    public function quoteName($name);

    public function makeQueryString(Query $query) : string;

}