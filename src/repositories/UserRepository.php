<?php

namespace App\repositories;

use App\models\User;

/**
 * @method fetchOne(array $params, array $order = []) : User
 * @method fetchMany(array $params, array $order = []) : User[]
 * @method fetchAll(array $order = []) : User[]
 * @method save(User $user, array $filters) : UserRepository
 * @method create(User $user) : UserRepository
 * @method delete(User $user) : UserRepository
 */
class UserRepository extends Repository {
    protected $table = User::TABLE_NAME;
    protected $fields = ["id", "email", "first_name", "last_name"];
    protected $currentClass = User::class;
}