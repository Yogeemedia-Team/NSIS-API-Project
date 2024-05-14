<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;

interface StudentAdminssionInterface {

    public function store(array $data);
    
    public function getAll(array $filterData);

    public function getById(int $id): object|null;

    public function update(array $data);
    
}