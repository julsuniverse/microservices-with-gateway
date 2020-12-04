<?php

declare(strict_types=1);

namespace App\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * Class AllowAccess
 * @package App\Annotations
 */
class AllowAccess
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_GUEST = 'ROLE_GUEST';

    public $roles;

    public function isGuest(): bool
    {
        return in_array(self::ROLE_GUEST, $this->roles);
    }
}