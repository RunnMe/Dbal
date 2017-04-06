<?php

namespace Running\tests\Dbal;

use PHPUnit_Extensions_Database_TestCase;

abstract class DeployDBUnit extends PHPUnit_Extensions_Database_TestCase
{
    protected static $settings;

    public static function getSettings()
    {
        if (empty(self::$settings)) {
            self::$settings = require(__DIR__ . '/../connectionsSettings.php');
        }
        return self::$settings;
    }
}
