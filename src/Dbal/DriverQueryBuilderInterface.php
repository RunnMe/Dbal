<?php

namespace Running\Dbal;

/**
 * Interface DriverQueryBuilderInterface
 * @package Running\Dbal
 *
 * @codeCoverageIgnore
 */
interface DriverQueryBuilderInterface
{

    public function quoteName($name);

    public function makeQueryString(Query $query) : string;

}