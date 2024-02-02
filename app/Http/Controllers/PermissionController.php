<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\PermissionRepository;



class PermissionController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public $permissionRepository;

    public function __construct(PermissionRepository $PermissionRepository)
    {
        $this->permissionRepository = $PermissionRepository;
    }

  


    public function index() : JsonResponse
    {
        try {
            
            return $this->responseSuccess($this->permissionRepository->getPermission(), 'permission fetch success');
        } catch (Exception $e) {
            return $this->responseError([],  $e->getMessage());
        }
        

        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
