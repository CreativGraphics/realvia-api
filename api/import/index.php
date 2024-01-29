<?php

require_once  "../../autoload.php";

use RealviaApi\Realvia;
use RealviaApi\Util\DotEnv;

(new DotEnv("../../.env"))->load();

$realvia = new Realvia();

$realvia->enableLogger("api/import/logs");

$realvia->handleRequest();
