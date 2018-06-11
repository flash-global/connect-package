<?php

namespace Fei\Service\Connect\Package\Middleware;

use Fei\Service\Connect\Package\Exception\RoleNotAllowedException;
use Fei\Service\Connect\Package\UserAwareInterface;
use Fei\Service\Connect\Package\UserAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AllowedRolesMiddleware
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect\Middleware
 */
class AllowedRolesMiddleware implements MiddlewareInterface, UserAwareInterface
{
    use UserAwareTrait;

    /**
     * @var array
     */
    protected $roles;

    /**
     * AllowedRolesMiddleware constructor.
     *
     * @param array $roles
     */
    public function __construct(array $roles)
    {
        $this->setRoles($roles);
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!in_array($this->getUser()->getCurrentRole(), $this->getRoles())) {
            throw new RoleNotAllowedException(
                sprintf(
                    'The user\'s current role "%s" is not allowed. "%s" allowed.',
                    $this->getUser()->getCurrentRole(),
                    implode('", "', $this->getRoles())
                )
            );
        }

        return $handler->handle($request);
    }

    /**
     * Get Roles
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Set Roles
     *
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }
}
