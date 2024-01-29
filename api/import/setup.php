<?php

require_once  "../../autoload.php";

use RealviaApi\Database\Database;
use RealviaApi\Model\Broker;
use RealviaApi\Model\Realestate;
use RealviaApi\Util\DotEnv;

$db = new Database(DotEnv::get("DB_HOST"), DotEnv::get("DB_PORT"), DotEnv::get("DB_USER"), DotEnv::get("DB_PASS"), DotEnv::get("DB_NAME"));
$db->createTable(Broker::class);
$db->createTable(Realestate::class);