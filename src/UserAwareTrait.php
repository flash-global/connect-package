<?php

namespace Fei\Service\Connect\Package;

use Fei\Service\Connect\Common\Entity\User;

/**
 * Trait UserAwareTrait
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect
 */
trait UserAwareTrait
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Get User
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set User
     *
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}
