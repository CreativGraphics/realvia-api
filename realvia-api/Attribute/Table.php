<?php

namespace RealviaApi\Attribute;

use Attribute;

#[Attribute]
class Table {
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}