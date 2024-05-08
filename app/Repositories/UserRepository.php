<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\User;
use App\Interfaces\UserInterface ;
use App\Models\StudentDetail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\PersonalAccessTokenResult;
use Carbon\Carbon;

class UserRepository implements UserInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        $query = User::with('user_type')->get(); 
        // $query = UserLevel::orderBy($filter['orderBy'], $filter['order']);

        // if (!empty($filter['search'])) {
        //     $query->where(function ($query) use ($filter) {
        //         $searched = '%' . $filter['search'] . '%';
        //         $query->where('level', 'like', $searched)
        //         ->orWhere('title', 'like', $searched);
        //     });
        // }
        return $query;
    }

    public function studentLists(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        $query = StudentDetail::get(); 
        // $query = UserLevel::orderBy($filter['orderBy'], $filter['order']);

        // if (!empty($filter['search'])) {
        //     $query->where(function ($query) use ($filter) {
        //         $searched = '%' . $filter['search'] . '%';
        //         $query->where('level', 'like', $searched)
        //         ->orWhere('title', 'like', $searched);
        //     });
        // }
        return $query;
    }
    public function getFilterData(array $filterData): array
    {
        $defaultArgs = [
            'perPage' => 10,
            'search' => '',
            'orderBy' => 'id',
            'order' => 'desc'
        ];

        return array_merge($defaultArgs, $filterData);
    }

    public function getById(int $id): ?User
    {
        $user_level = User::find($id);

        if (empty($user_level)) {
            throw new Exception("User level does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $user_level;
    }

    public function create(array $data): array
    {
        $user = User::create($this->prepareForDB($data));

        if (!$user) {
            throw new Exception("Sorry, user does not registered, Please try again.", 500);
        }

        $tokenInstance = $this->createAuthToken($user);

        return $this->getAuthData($user, $tokenInstance);
    }

    public function update(int $id, array $data): ?User
    {
        $user = $this->getById($id);

        $updated = $user->update($this->prepareForDB($data, $user));

        if ($updated) {
            $user = $this->getById($id);
        }

        return $user;
    }

    public function delete(int $id): ?User
    {
        $user_level = $this->getById($id);
        $deleted = $user_level->delete();

        if (!$deleted) {
            throw new Exception("User level could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $user_level;
    }

    public function getAuthData(User $user, PersonalAccessTokenResult $tokenInstance)
    {
        return [
            'access_token' => $tokenInstance->accessToken,
            'expires_at'   => Carbon::parse($tokenInstance->token->expires_at)->toDateTimeString()
        ];
    }

     public function createAuthToken(User $user): PersonalAccessTokenResult
    {
        return $user->createToken('authToken');
    }

    public function prepareForDB(array $data, ?User $user_level = null): array
    {
        return [
            'name' => $data['name'],
            'user_type' => $data['user_type'],
            'client_secret' => Hash::make($data['client_secret']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];
    }

    




}