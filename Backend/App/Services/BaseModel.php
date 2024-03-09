<?php

namespace App\Services;

use Medoo\Medoo;
use App\Interfaces\CrudInterface;
use Exception;
use App\Services\Validation;
use App\Services\Validation\ValueValidation;

class BaseModel implements CrudInterface
{
    protected $connection;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct($table)
    {
        $this->table = $table;
        try {
            global $databaseCongif;
            $this->connection = new Medoo(['type' => $databaseCongif['type'], 'host' => $databaseCongif['host'], 'database' => $databaseCongif['dbName'], 'username' => $databaseCongif['username'], 'password' => $databaseCongif['password']]);
        } catch (Exception $error) {
            echo $error->getMessage();
        }
    }
    # create function
    public function create(array $data): int
    {
        if (!ValueValidation::isValidArrayArguments($data))
            return false;
        $this->connection->insert($this->table, $data);
        return $this->connection->id();
    }
    # get all items function
    public function getAll(): array
    {
        return $this->connection->select($this->table, '*');
    }

    public function get(array $culomns, array $where): array
    {
        return $this->connection->select($this->table, $culomns, $where);
    }
    # find by id
    public function find(int $id): object
    {
        $result = $this->connection->get($this->table, '*', [$this->primaryKey => $id]);
        return (object)$result;
    }
    # delete function (return row count)
    public function delete(array $where): int
    {
        $result = $this->connection->delete($this->table, $where);
        return $result->rowCount();
    }
    public function update($data, $where){
        $result = $this->connection->update($this->table, $data,$where);
        return $result->rowCount();
    }
}