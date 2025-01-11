<?php

namespace App\Repositories;

use App\Models\User;


class UserRepository
{
    protected $model;

    // Inject the model into the repository
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    // Get all users
    public function all()
    {
        return $this->model->all();
    }

    // Find a user by ID
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    // Create a new user
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // Update an existing user
    public function update($id, array $data)
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);
        return $user;
    }

    // Delete a user
    public function delete($id)
    {
        $user = $this->model->findOrFail($id);
        return $user->delete();
    }

    public function findMany(array $ids)
    {
        return $this->model->findMany($ids);
    }
}
