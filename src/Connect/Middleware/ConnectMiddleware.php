<?php

namespace ObjectivePHP\Package\Connect\Middleware;

use Fei\Service\Connect\Client\Connect;
use ObjectivePHP\Application\ApplicationInterface;

/**
 * Class ConnectMiddleware
 *
 * @package ObjectivePHP\Package\Connect
 */
class ConnectMiddleware
{
    /**
     * Handle request by Connect client
     *
     * @param ApplicationInterface $app
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ApplicationInterface $app)
    {
        /** @var Connect $connect */
        $connect = $app->getServicesFactory()->get('connect.client');

        $connect->handleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

        return $connect->getResponse();
    }
}
