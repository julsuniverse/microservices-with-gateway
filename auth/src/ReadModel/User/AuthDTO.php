<?php

declare(strict_types=1);

namespace App\ReadModel\User;

class AuthDTO
{
    public $id;
    public $email;
    public $password_hash;
    public $role;

    public function __construct($id, $email, $password_hash, $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->role = $role;
    }
}