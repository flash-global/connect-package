<?php

namespace Tests\Fei\Connect\Package\Middleware;

use Fei\Service\Connect\Common\Entity\User;
use Fei\Service\Connect\Package\Exception\RoleNotAllowedException;
use Fei\Service\Connect\Package\Middleware\AllowedRolesMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

/**
 * Class AllowedRolesMiddlewareTest
 *
 * @package Tests\Fei\Connect\Package\Injector
 */
class AllowedRolesMiddlewareTest extends TestCase
{
    public function testRoleIsAllowed()
    {
        $middleware = new AllowedRolesMiddleware(['USER', 'ADMIN']);

        $middleware->setUser((new User())->setCurrentRole('ADMIN'));

        $this->assertEquals(['USER', 'ADMIN'], $middleware->getRoles());
        $this->assertAttributeEquals($middleware->getRoles(), 'roles', $middleware);

        $response = new Response();

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->willReturn($response);

        $this->assertEquals($response, $middleware->process(new ServerRequest(), $handler));
    }

    public function testRoleIsNotAllowed()
    {
        $middleware = new AllowedRolesMiddleware(['USER', 'ADMIN']);

        $middleware->setUser((new User())->setCurrentRole('TEST'));

        $this->assertEquals(['USER', 'ADMIN'], $middleware->getRoles());
        $this->assertAttributeEquals($middleware->getRoles(), 'roles', $middleware);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $this->expectException(RoleNotAllowedException::class);
        $this->expectExceptionMessage('The user\'s current role "TEST" is not allowed. "USER", "ADMIN" allowed.');

        $middleware->process(new ServerRequest(), $handler);
    }
}
