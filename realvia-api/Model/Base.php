<?php

namespace RealviaApi\Model;

use RealviaApi\Attribute\Column;
use ReflectionClass;

abstract class Base {

    public abstract function getId(): ?int;

    public static function fromJson($json): self
    {
        $className = get_called_class();

        $instance = new $className;

        $columns = $instance::getColumns();

        $data = json_decode($json, true);

        foreach ($columns as $column) {
            $name = $column['property']->name;
            $field = $column['attribute']->field;

            if($name == "rawData") {
                $instance->setProperty("rawData", $json);
            }

            if($field){
                $path = explode(".", $field);

                $temp = &$data;

                foreach ($path as $key) {
                    if(gettype($temp) == "array") $temp = &$temp[$key];
                }

                $value = $temp;

                unset($temp);

                $instance->setProperty($name, $value);
            }
        }

        return $instance;
    }

    public static function fromResult(array $result): self
    {
        $className = get_called_class();

        $instance = new $className;

        $columns = $instance::getColumns();

        foreach ($columns as $column) {

            if($column["attribute"]->type == "json"){
                $instance->setProperty($column["property"]->name, json_decode($result[$column["property"]->name]));
            } else {
                $instance->setProperty($column["property"]->name, $result[$column["property"]->name]);
            }
        }

        return $instance;
    }

    public static function getColumns(): array
    {
        $columns = [];

        $clazz = new ReflectionClass(get_called_class());

        $properties = $clazz->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Column::class);

            if(sizeof($attributes) > 0){
                array_push($columns, ['property' => $property, 'attribute' => $attributes[0]->newInstance()]);
            }
        }

        return $columns;
    }

    public function getFilledColumns(): array
    {
        $columns = [];

        $allColumns = $this::getColumns();

        foreach ($allColumns as $column) {
            array_push($columns, [
                'property' => $column["property"],
                'attribute' => $column["attribute"],
                'value' => $this->getProperty($column["property"]->name)
            ]);
        }

        return $columns;
    }

    public function getProperty($property){
        $method = "get" . str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));

        if(method_exists(get_called_class(), $method)){
            return $this->$method();
        }

        return null;
    }

    public function setProperty($property, $value)
    {
        $method = "set" . str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));

        if(method_exists(get_called_class(), $method)){
            $this->$method($value);
        }
    }
}