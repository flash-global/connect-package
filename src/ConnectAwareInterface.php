<?php

namespace Fei\Service\Connect\Package;

use Fei\Service\Connect\Client\Connect;

/**
 * Interface ConnectAwareInterface
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect
 */
interface ConnectAwareInterface
{
    /**
     * Set Connect Client
     *
     * @return Connect
     */
    public function getConnect(): Connect;

    /**
     * Get Connect Client
     *
     * @param Connect $connect
     *
     * @return mixed
     */
    public function setConnect(Connect $connect);
}
