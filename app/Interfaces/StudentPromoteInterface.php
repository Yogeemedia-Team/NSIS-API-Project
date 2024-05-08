<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;

interface StudentPromoteInterface {
    public function promote(array $data): object|null;
}