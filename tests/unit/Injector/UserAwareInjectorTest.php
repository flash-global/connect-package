<?php

namespace Tests\Fei\Connect\Package\Injector;

use Fei\Service\Connect\Common\Entity\User;
use Fei\Service\Connect\Package\Injector\UserAwareInjector;
use Fei\Service\Connect\Package\UserAwareInterface;
use Fei\Service\Connect\Package\UserAwareTrait;
use ObjectivePHP\ServicesFactory\ServicesFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class UserAwareInjectorTest
 *
 * @package Tests\Fei\Connect\Package\Injector
 */
class UserAwareInjectorTest extends TestCase
{
    public function testInjection()
    {
        $injector = new UserAwareInjector('service.test');

        $instance = new class implements UserAwareInterface {
            use UserAwareTrait;
        };

        $user = $this->createMock(User::class);

        $servicesFactory = $this->createMock(ServicesFactory::class);

        $servicesFactory->expects($this->once())->method('get')->with('service.test')->willReturn($user);

        $injector->injectDependencies($instance, $servicesFactory);

        $this->assertEquals($user, $instance->getUser());
    }

    public function testNoInjection()
    {
        $injector = new UserAwareInjector('service.test');

        $instance = new class {
        };

        $servicesFactory = $this->createMock(ServicesFactory::class);
        $servicesFactory->expects($this->never())->method('get');

        $injector->injectDependencies($instance, $servicesFactory);
    }
}
