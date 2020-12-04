<?php

declare(strict_types=1);

namespace App\Security;

class User
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var array
     */
    public $roles;

    public function __construct(string $id, array $roles)
    {
        $this->id = $id;
        $this->roles = $roles;
    }
}