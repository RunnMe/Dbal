<?php

namespace Running\tests\Dbal;

use PHPUnit_Extensions_Database_TestCase;

abstract class DeployDBUnit extends PHPUnit_Extensions_Database_TestCase
{
    protected $settings;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->settings = require(__DIR__ . '/../connectionsSettings.php');
        parent::__construct($name, $data, $dataName);
    }
}
