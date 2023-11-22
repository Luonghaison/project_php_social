<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function create(array $data);

    public function login(array $data);

    public function changePassword($userId, $newPassword);
}
