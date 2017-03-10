<?php

namespace Running\Dbal;

use Running\Core\Std;

abstract class Index extends Std
{
    protected static $required = ['columns'];

    protected function needCasting($key, $value): bool
    {
        if ('columns' == $key) {
            return false;
        } else {
            return parent::needCasting($key, $value);
        }
    }
}
