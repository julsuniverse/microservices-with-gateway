<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Update;

use App\Model\User\Entity\Id;
use App\Model\User\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @var Id
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @var string
     */
    public $email;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
    }
}