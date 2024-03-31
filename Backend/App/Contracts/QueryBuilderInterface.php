<?php

namespace App\Contracts;

interface QueryBuilderInterface
{
    public function where(string $column, string $value);
    public function create(array $data);
    public function update(array $data);
    public function delete();
    public function get(array $columns = ['*']);
    public function first(array $columns);
    public function find(int $id);
    public function findBy(string $columns, $value);
    public function count();
}
