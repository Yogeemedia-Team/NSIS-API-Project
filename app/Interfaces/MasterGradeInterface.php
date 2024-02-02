<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;

interface MasterGradeInterface {
    public function getAll(array $filterData);

    public function getById(int $id): object|null;

    public function create(array $data): object|null;

    public function update(array $data, int $id): object|null;

    public function delete(int $id): object|null;
}