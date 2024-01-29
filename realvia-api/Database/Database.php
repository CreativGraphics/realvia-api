<?php

namespace RealviaApi\Database;

use Exception;
use mysqli;
use RealviaApi\Attribute\Table;
use RealviaApi\Model\Base;
use ReflectionClass;

class Database {

    private $connection;
    private $query;
    private $query_count = 0;

    public function __construct(string $host, int|string $port, string $user, string $pass, string $name, string $charset = "utf8" )
    {
        $this->connection = new mysqli($host, $user, $pass, $name, gettype($port) == "int" ? $port : intval($port));
        $this->connection->set_charset($charset);
    }

    public function createTable(Base|string $fcqn)
    {
        $tableName = $this->getTableName($fcqn);

        $query = "CREATE TABLE IF NOT EXISTS {$tableName} (\n";

        $columns = $fcqn::getColumns();

        $first = true;

        foreach ($columns as $column) {
            $name = $column["property"]->getName();
            $attribute = $column["attribute"];

            $type = strtoupper($attribute->type);
            $primary = $attribute->primary;
            $length = $attribute->length;

            $query .= ($first ? "" : ",\n");

            if($type == "RELATION") {

                $relationClass = $attribute->relationClass;
                $relationField = $attribute->relationField;

                if(!class_exists($relationClass)) $this->error("Relation class does not exists: " . $relationClass);
                if(!property_exists($relationClass, $relationField)) $this->error("Relation class " . $relationClass . " does not contain property " . $relationField);

                $query .= "{$name} INT, FOREIGN KEY ({$name}) REFERENCES " . $this->getTableName($relationClass) . "({$relationField})";
            } else {
                $query .= "{$name} {$type}". ($length != null ? "({$length})" : "") . ($primary ? " PRIMARY KEY" : "");
            }

            $first = false;
        }

        $query .= "\n)";

        $this->query($query);
    }

    private function getTableName(Base|string $fcqn): string
    {
        $clazz = new ReflectionClass($fcqn);

        $attributes = $clazz->getAttributes(Table::class);

        if(sizeof($attributes) == 0){
            $this->error("Cannot determine table name for class " . $fcqn . ". Table attribute is missing.");
        }

        $tableAttribute = $attributes[0]->newInstance();

        return $tableAttribute->name;
    }

    public function query($query) {
        if($this->query) $this->query->close();
		if ($this->query = $this->connection->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_slice($x, 1);
				$types = '';
                $args_ref = array();
                foreach ($args as $k => &$arg) {
					if (is_array($args[$k])) {
						foreach ($args[$k] as $j => &$a) {
							$types .= $this->_gettype($args[$k][$j]);
							$args_ref[] = &$a;
						}
					} else {
	                	$types .= $this->_gettype($args[$k]);
	                    $args_ref[] = &$arg;
					}
                }
				array_unshift($args_ref, $types);
                call_user_func_array(array($this->query, 'bind_param'), $args_ref);
            }
            $this->query->execute();
           	if ($this->query->errno) {
				$this->error('Unable to process MySQL query (check your params) - ' . $this->query->error);
           	}
			$this->query_count++;
        } else {
            $this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error);
        }
		return $this;
    }

    public function insert(Base $model)
    {
        $columns = $model->getFilledColumns();

        $query = "INSERT INTO " . $this->getTableName($model) . " (";

        $first = true;

        $values = [];

        $formattedValues = [];

        foreach ($columns as $column) {
            $query .= ($first ? "" : ",");

            $query .= $column["property"]->name;

            array_push($values, "?");

            if($column["attribute"]->type == "json"){
                array_push($formattedValues, json_encode($column["value"]));
            } else {
                array_push($formattedValues, $column["value"]);
            }

            $first = false;
        }

        $query .= ") VALUES (" . implode(",", $values) . ")";

        $this->query($query, ...$formattedValues);
    }

    public function findAll(string|Base $model, ?string $order = null, ?int $limit = null): array
    {
        $results = [];

        $query = "SELECT * FROM " . $this->getTableName($model);

        if ($order != null) {
            $query .= " ORDER BY " . $order;
        }

        if ($limit != null) {
            $query .= " LIMIT " . $limit;
        }

        $this->query($query);

        $result = $this->query->get_result();

        while($row = $result->fetch_assoc()) {
            array_push($results, $model::fromResult($row));
        }

        return $results;
    }

    public function find(string|Base $model, int $id): ?Base
    {
        $results = [];

        $query = "SELECT * FROM " . $this->getTableName($model) . " WHERE id = ?";

        $this->query($query, $id);

        $result = $this->query->get_result();

        while($row = $result->fetch_assoc()) {
            array_push($results, $model::fromResult($row));
        }

        return sizeof($results) > 0 ? $results[0] : null;
    }

    public function findBy(string|Base $model, array $where, ?string $order = null, ?int $limit = null): array
    {
        $results = [];

        $query = "SELECT * FROM " . $this->getTableName($model);

        $first = true;

        $values = [];

        foreach ($where as $key => $value) {
            $query .= (!$first ? " AND " : "");
            $query .= " WHERE " . $key . " = ?";
            array_push($values, $value);
            $first = false;
        }

        if ($order != null) {
            $query .= " ORDER BY " . $order;
        }

        if ($limit != null) {
            $query .= " LIMIT " . $limit;
        }

        $this->query($query, ...$values);

        $result = $this->query->get_result();

        while($row = $result->fetch_assoc()) {
            array_push($results, $model::fromResult($row));
        }

        return $results;
    }

    public function update(Base $model)
    {
        $columns = $model->getFilledColumns();

        $query = "UPDATE " . $this->getTableName($model) . "SET ";

        $values = [];

        $formattedValues = [];

        foreach ($columns as $column) {
            $query .= $column["property"]->name;
            array_push($values, $column["property"]->name . " = ?");

            if($column["attribute"]->type == "json"){
                array_push($formattedValues, json_encode($column["value"]));
            } else {
                array_push($formattedValues, $column["value"]);
            }
        }

        array_push($formattedValues, $model->getId());

        $query = "UPDATE " . $this->getTableName($model) . " SET " . implode(", ", $values) . " WHERE id = ?";

        $this->query($query, ...$formattedValues);
    }

    public function delete(Base $model)
    {
        $query = "DELETE FROM " . $this->getTableName($model) . " WHERE id = ?";

        $this->query($query, $model->getId());
    }

    public function fetchArray() {
	    $params = array();
        $row = array();
	    $meta = $this->query->result_metadata();
	    while ($field = $meta->fetch_field()) {
	        $params[] = &$row[$field->name];
	    }
	    call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
		while ($this->query->fetch()) {
			foreach ($row as $key => $val) {
				$result[$key] = $val;
			}
		}
		return $result;
	}

    public function error($error) {
        throw new Exception($error);
    }

    public function _getType($var): string
    {
        if (is_string($var)) return 's';
	    if (is_float($var)) return 'd';
	    if (is_int($var)) return 'i';
	    return 'b';
    }
}