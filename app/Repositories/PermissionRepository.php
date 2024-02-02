<?php

namespace App\Repositories;
use App\Models\Permission;
use App\Interfaces\CrudInterface;

class PermissionRepository implements CrudInterface{
    public function getPermission(){
        return  $permissions = Permission::get();
    }
}