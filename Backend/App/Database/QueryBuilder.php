<?php

namespace App\Database;

use App\Contracts\QueryBuilderInterface;
use PDO;

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

    public function count()
    {
        $sql = "SELECT COUNT(id) AS count FROM {$this->table}";
        $this->execute($sql);
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
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

    public function update(array $data)
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

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statement->rowCount();
    }

    public function get(array $columns = ['*'])
    {
        $fields = implode(', ', $columns);
        $sql = "SELECT {$fields} FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statement->fetchAll();
    }

    public function first(array $columns = ['*'])
    {
        $data = $this->get($columns);
        return empty($data) ? null : $data[0];
    }

    public function find(int $id)
    {
        return $this->where('id', $id)->first();
    }

    public function findBy(string $column, $value)
    {
        return $this->where($column, $value)->first();
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
    public function __destruct()
    {
        $this->connection = null;
    }
}
