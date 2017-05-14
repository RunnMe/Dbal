<?php

require __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function ($class) {
    if (preg_match('~^Runn~', $class)) {
        $file = __DIR__ . '/../src' . str_replace('\\', '/', preg_replace('~^Runn~', '', $class)) . '.php';
        if (is_readable($file)) {
            require $file;
        }
    }
});