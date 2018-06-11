<?php

namespace Fei\Service\Connect\Package\Injector;

use Fei\Service\Connect\Package\ConnectAwareInterface;
use ObjectivePHP\ServicesFactory\Injector\InjectorInterface;
use ObjectivePHP\ServicesFactory\ServicesFactory;
use ObjectivePHP\ServicesFactory\Specification\ServiceSpecificationInterface;

/**
 * Class ConnectAwareInjector
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect\Injector
 */
class ConnectAwareInjector implements InjectorInterface
{
    use ServiceIdAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function injectDependencies(
        $instance,
        ServicesFactory $servicesFactory,
        ServiceSpecificationInterface $serviceSpecification = null
    ) {
        if ($instance instanceof ConnectAwareInterface) {
            $instance->setConnect($servicesFactory->get($this->getServiceId()));
        }
    }
}
