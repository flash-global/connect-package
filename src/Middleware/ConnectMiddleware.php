<?php

namespace Fei\Service\Connect\Package\Middleware;

use Fei\Service\Connect\Package\ConnectAwareInterface;
use Fei\Service\Connect\Package\ConnectAwareTrait;
use ObjectivePHP\Filter\FiltersProviderTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ConnectMiddleware
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect\Middleware
 */
class ConnectMiddleware implements MiddlewareInterface, ConnectAwareInterface
{
    use ConnectAwareTrait, FiltersProviderTrait;

    public function __construct()
    {
        $this->initFiltersCollection();
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->getConnect()->handleRequest($request->getUri()->getPath(), $request->getMethod());

        return $this->getConnect()->getResponse() ?: $handler->handle($request);
    }
}
