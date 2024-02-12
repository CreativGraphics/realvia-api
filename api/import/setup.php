<?php

require_once  "../../autoload.php";

use RealviaApi\Model\Broker;
use RealviaApi\Model\Realestate;
use RealviaApi\Realvia;
use RealviaApi\Util\DotEnv;

(new DotEnv("../../.env"))->load();

$realvia = new Realvia();

$db = $realvia->getDatabase();
$db->createTable(Broker::class);
$db->createTable(Realestate::class);