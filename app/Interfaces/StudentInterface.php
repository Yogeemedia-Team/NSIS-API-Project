<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;

interface StudentInterface {
    public function getAll(array $filterData);

    public function getById($id): object|null;

    public function create(array $data): object|null;

    public function update(array $data, $studentId): object|null;

    public function delete($id): object|null;
}