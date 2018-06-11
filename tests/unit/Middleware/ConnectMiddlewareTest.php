<?php

namespace Tests\Fei\Connect\Package\Middleware;

use Fei\Service\Connect\Client\Connect;
use Fei\Service\Connect\Package\Middleware\ConnectMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

/**
 * Class ConnectMiddlewareTest
 *
 * @package Tests\Fei\Connect\Package\Middleware
 */
class ConnectMiddlewareTest extends TestCase
{
    public function testConnectResponded()
    {
        $middleware = new ConnectMiddleware();

        $response = new Response();

        $connect = $this->createMock(Connect::class);
        $connect->expects($this->once())->method('handleRequest')->with('/test', 'POST');
        $connect->expects($this->once())->method('getResponse')->willReturn($response);

        $middleware->setConnect($connect);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $this->assertEquals($response, $middleware->process(new ServerRequest([], [], '/test', 'POST'), $handler));
    }

    public function testConnectDoesNotResponded()
    {
        $middleware = new ConnectMiddleware();

        $response = new Response();

        $connect = $this->createMock(Connect::class);
        $connect->expects($this->once())->method('handleRequest')->with('/test', 'POST');
        $connect->expects($this->once())->method('getResponse')->willReturn(null);

        $middleware->setConnect($connect);

        $request = new ServerRequest([], [], '/test', 'POST');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->with($request)->willReturn($response);

        $this->assertEquals($response, $middleware->process($request, $handler));
    }
}
