<?php

namespace Running\Dbal;

/**
 * Interface IDriverQuery
 * @package Running\Dbal
 *
 * @codeCoverageIgnore
 */
interface IDriverQuery
{

    public function makeQueryString(Query $query) : string;

}