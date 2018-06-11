<?php

namespace Fei\Service\Connect\Package;

use Fei\Service\Connect\Client\Connect;

/**
 * Trait ConnectAwareTrait
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect
 */
trait ConnectAwareTrait
{
    /**
     * @var Connect
     */
    protected $connect;

    /**
     * Get Connect
     *
     * @return Connect
     */
    public function getConnect(): Connect
    {
        return $this->connect;
    }

    /**
     * Set Connect
     *
     * @param Connect $connect
     *
     * @return $this
     */
    public function setConnect(Connect $connect)
    {
        $this->connect = $connect;

        return $this;
    }
}
