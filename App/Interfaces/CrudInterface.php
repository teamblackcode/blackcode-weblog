<?php

namespace App\Interfaces;

interface CrudInterface
{
    # create function
    public function create(array $data): int;
    # get all items function
    public function getAll(): array;
    # get culomns function
    public function get(array $culomns, array $where): array;
    # find a item by id function 
    public function find(int $id): object;
    # delete function 
    public function delete(array $where): int;
}
