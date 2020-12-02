<?php

declare(strict_types=1);

namespace App\Model\User\Entity;

use App\Model\User\Entity\Role;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="users", uniqueConstraints={
 *      @ORM\UniqueConstraint(columns={"email"}),
 * })
 * Class User
 * @package Model\User\Entity
 */
class User
{
    /**
     * @ORM\Column(type="user_user_id")
     * @ORM\Id
     * @var Id
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", name="password_hash")
     * @var string
     */
    private $passwordHash;

    /**
     *  @ORM\Column(type="datetime_immutable")
     *  @var \DateTimeImmutable
     */
    private $date;

    /**
     * @ORM\Column(type="user_user_role")
     */
    private $role;

    public function __construct(Id $id, string $email, string $passwordHash, \DateTimeImmutable $date)
    {
        $this->id = $id->getValue();
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->date = $date;
        $this->role = Role::user();
    }

    public function edit(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    public function changeRole(Role $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}