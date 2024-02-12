<?php

namespace RealviaApi\Attribute;

use Attribute;

#[Attribute]
class Column
{
    public string $type;
    public ?int $length;
    public ?string $field;
    public bool $primary;
    public bool $nullable;
    public ?string $relationClass;
    public ?string $relationField;

    public function __construct(string $type = "varchar", ?int $length = null, ?string $field = null, bool $primary = false, bool $nullable = true, string $relationClass = null, string $relationField = null)
    {
        $this->type = $type;
        $this->length = $length;
        $this->field = $field;
        $this->primary = $primary;
        $this->nullable = $nullable;
        $this->relationClass = $relationClass;
        $this->relationField = $relationField;
    }
}
