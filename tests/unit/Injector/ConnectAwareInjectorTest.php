<?php

namespace Tests\Fei\Connect\Package\Injector;

use Fei\Service\Connect\Client\Connect;
use Fei\Service\Connect\Package\ConnectAwareInterface;
use Fei\Service\Connect\Package\ConnectAwareTrait;
use Fei\Service\Connect\Package\Injector\ConnectAwareInjector;
use ObjectivePHP\ServicesFactory\ServicesFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class ConnectAwareInjectorTest
 *
 * @package Tests\Fei\Connect\Package\Injector
 */
class ConnectAwareInjectorTest extends TestCase
{
    public function testInjection()
    {
        $injector = new ConnectAwareInjector('service.test');

        $instance = new class implements ConnectAwareInterface {
            use ConnectAwareTrait;
        };

        $connect = $this->createMock(Connect::class);

        $servicesFactory = $this->createMock(ServicesFactory::class);

        $servicesFactory->expects($this->once())->method('get')->with('service.test')->willReturn($connect);

        $injector->injectDependencies($instance, $servicesFactory);

        $this->assertEquals($connect, $instance->getConnect());
    }

    public function testNoInjection()
    {
        $injector = new ConnectAwareInjector('service.test');

        $instance = new class {
        };

        $servicesFactory = $this->createMock(ServicesFactory::class);
        $servicesFactory->expects($this->never())->method('get');

        $injector->injectDependencies($instance, $servicesFactory);
    }
}
