<?php

define('BASE_DIR', __DIR__);

spl_autoload_register(function (string $className): void {
    include  BASE_DIR . DIRECTORY_SEPARATOR . str_replace('RealviaApi', 'realvia-api', str_replace('\\', DIRECTORY_SEPARATOR, $className)) . '.php';
});