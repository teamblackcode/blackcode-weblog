<?php

namespace App\Contracts;

interface QueryBuilderInterface
{
    public function where(string $column, string $value);
    public function create(array $data): int;
    public function fetch(array $columns): object;
    public function update(array $data): int;
    public function delete(int $id): int;
    public function get(array $columns): object;
    public function first(array $columns): object;
    public function findBy(string $columns, $value): object;
}
