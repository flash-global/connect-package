<?php

namespace Fei\Service\Connect\Package\Injector;

/**
 * Trait ServiceIdAwareTrait
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect\Injector
 */
trait ServiceIdAwareTrait
{
    /**
     * @var string
     */
    protected $serviceId;

    /**
     * ConnectAwareInjector constructor.
     *
     * @param string $serviceId
     */
    public function __construct(string $serviceId)
    {
        $this->setServiceId($serviceId);
    }

    /**
     * Get ServiceId
     *
     * @return string
     */
    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    /**
     * Set ServiceId
     *
     * @param string $serviceId
     *
     * @return $this
     */
    public function setServiceId(string $serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }
}
