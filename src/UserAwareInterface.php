<?php

namespace Fei\Service\Connect\Package;

use Fei\Service\Connect\Common\Entity\User;

/**
 * Class UserAwareInterface
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect
 */
interface UserAwareInterface
{
    /**
     * Set Connect User
     *
     * @return User
     */
    public function getUser(): User;

    /**
     * Get Connect User
     *
     * @param User $user
     *
     * @return mixed
     */
    public function setUser(User $user);
}
