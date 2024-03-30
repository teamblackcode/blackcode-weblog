<?php

namespace App\Database;

use App\Contracts\QueryBuilderInterface;

class QueryBuilder implements QueryBuilderInterface
{
    protected $table;
    protected $connection;
    protected $conditions;
    protected $values;
    protected $statement;

    public function __construct($connection)
    {
        $this->connection = $connection->getConnection();
    }

    public function where(string $column, string $value)
    {
        if (is_null($this->conditions)) {
            $this->conditions = "{$column} = ?";
        } else {
            $this->conditions .= " AND {$column} = ?";
        }
        $this->values[] = $value;
        return $this;
    }

    public function table($tableName)
    {
        $this->table = $tableName;
        return $this;
    }

    public function create(array $data): int
    {
        $placeholder = [];
        foreach ($data as $column => $key) {
            $placeholder[] = '?';
        }
        $fields = implode(', ', array_keys($data));
        $placeholder = implode(', ', $placeholder);
        $this->values = array_values($data);
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholder})";
        $this->execute($sql);
        return (int)$this->connection->lastInsertId();
    }

    public function fetch(array $columns): object
    {
        return (object)[];
    }

    public function update(array $data): int
    {
        $fields = [];
        foreach ($data as $column => $value) {
            $fields[] = "{$column}='$value'";
        }
        $fields = implode(', ', $fields);
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statement->rowCount();
    }

    public function delete(int $id): int
    {
        return 5;
    }

    public function get(array $columns): object
    {
        return (object)[];
    }

    public function first(array $columns): object
    {
        return (object)[];
    }

    public function findBy(string $columns, $value): object
    {
        return (object)[];
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function rollback()
    {
        $this->connection->rollback();
    }

    public function execute($sql)
    {
        $this->statement = $this->connection->prepare($sql);
        $this->statement->execute($this->values);
        $this->values = [];
        return $this;
    }
}
