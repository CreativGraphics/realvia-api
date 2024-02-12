# Realvia import API

## Database setup

```php
<?php
// enable autoloader
require_once  "../../autoload.php";

use RealviaApi\Model\Broker;
use RealviaApi\Model\Realestate;
use RealviaApi\Realvia;
use RealviaApi\Util\DotEnv;

// import .env file with sensitive variables (Database user and password)
(new DotEnv("../../.env"))->load();

$realvia = new Realvia();

// get instance of the database
$db = $realvia->getDatabase();

// call createTable function with Model classes
$db->createTable(Broker::class);
$db->createTable(Realestate::class);
```

## Importing data

```php
<?php

// enable autoloader
require_once  "../../autoload.php";

use RealviaApi\Realvia;
use RealviaApi\Util\DotEnv;

// import .env file with sensitive variables (Database user and password)
(new DotEnv("../../.env"))->load();

$realvia = new Realvia();

// enable request logging
$realvia->enableLogger("api/import/logs");

// handle request and import data into database
$realvia->handleRequest();
```

## Getting data

```php
<?php

// enable autoloader
require_once  "../../autoload.php";

use RealviaApi\Realvia;
use RealviaApi\Util\DotEnv;

// import .env file with sensitive variables (Database user and password)
(new DotEnv("../../.env"))->load();

$realvia = new Realvia();

// get all realestate entries
$realestates = $realvia->getRealestate(?string $order = null, ?int $limit = null);

// get all broker entries
$brokers = $realvia->getBrokers(?string $order = null, ?int $limit = null);

// find realestate by broker
// $broker = instance of RealviaApi\Model\Broker class
$realestates = $realvia->getRealestateByBroker($broker, ?string $order = null, ?int $limit = null);

// find realestate and broker by it's is
$realestate = $realvia->findRealestate(int $id);
$broker = $realvia->findBroker(int $id);
```

## Other Database operations

```php
<?php

// enable autoloader
require_once  "../../autoload.php";

use RealviaApi\Realvia;
use RealviaApi\Util\DotEnv;

// import .env file with sensitive variables (Database user and password)
(new DotEnv("../../.env"))->load();

$realvia = new Realvia();

$database = $realvia->getDatabase();

// run any query
$database->query(string $query, ...$bindParams);

// find entry by ID
// $class = Realestate::class / Broker::class
$database->find(Realestate::class, int $id);

// find entry by column
// $class = Realestate::class / Broker::class
// $where = associative array of keys and values to search for
$database->findBy(Realestate::class, array $where, ?string $order = null, ?int $limit = null);

// find all entries by
// $class = Realestate::class / Broker::class
$database->findAll(Realestate::class, ?string $order = null, ?int $limit = null);

// insert, update and delete
// $class = instance of RealviaApi\Model\Broker|Realestate class
$database->insert($class);
$database->update($class);
$database->delete($class);
```