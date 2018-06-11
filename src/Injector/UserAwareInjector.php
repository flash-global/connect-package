<?php

namespace Fei\Service\Connect\Package\Injector;

use Fei\Service\Connect\Package\UserAwareInterface;
use ObjectivePHP\ServicesFactory\Injector\InjectorInterface;
use ObjectivePHP\ServicesFactory\ServicesFactory;
use ObjectivePHP\ServicesFactory\Specification\ServiceSpecificationInterface;

/**
 * Class UserAwareInjector
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect\Injector
 */
class UserAwareInjector implements InjectorInterface
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
        if ($instance instanceof UserAwareInterface) {
            $instance->setUser($servicesFactory->get($this->getServiceId()));
        }
    }
}
